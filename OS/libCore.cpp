#include "libCore.h"

Core::Core (pthread_mutex_t * p1, int num): screen(p1), allProc(num) {
    runingProc = 0;
    this->procStatusLock = new pthread_mutex_t;
    pthread_mutex_init(this->procStatusLock, NULL);
    this->currentProc = -1;
}

void Core::printProcStatus(int i) {
    if (this->editProc == i) {
        mt_setfgcolor(MT_RED);
    }
    printf("Процесс номер %d: ", i);
    mt_setfgcolor(MT_BLACK);
    if (!this->procQuantum[i]) {
        mt_setfgcolor(MT_RED);
        printf("нет квантов ");
    } else if (this->procStatus[i] == 2) {
        mt_setfgcolor(MT_RED);
        printf("остановлен  ");
    } else if (this->procStatus[i] == 1) {
        mt_setfgcolor(MT_GREEN);
        printf("работает    ");
    } else if (this->procStatus[i] == 0) {
        mt_setfgcolor(MT_YELLOW);
        printf("ожидает     ");
    } else if (this->procStatus[i] == 3) {
        mt_setfgcolor(MT_RED);
        printf("заблокирован");
    }
    mt_setfgcolor(MT_BLACK);
    printf(" квантов: %d   ", this->procQuantum[i]);
    printf("\n");
}
void Core::draw() {
    int i;
    pthread_mutex_lock(this->screen);
    bc_box(this->x, this->y, this->h, this->w);
    for (i = 0; i < this->allProc; i++) {
        mt_gotoXY(this->x+i+1,this->y+1);
        printProcStatus(i);
    }
    printf("\n");
    pthread_mutex_unlock(this->screen);
    this->drawSemaphore();
    this->drowCounter();
}

void Core::chooseTask() {
    int check = 1;
    pthread_mutex_lock(this->procStatusLock);
    if (this->currentProc == -1) this->currentProc = 0;
    if (this->procStatus[this->currentProc] == 1) this->procStatus[this->currentProc] = 0;
    while(check <= this->allProc) {
        int checkProc = (this->currentProc + check) % this->allProc;
        if (this->procStatus[checkProc] == 0 && this->procQuantum[checkProc]) {
            this->procStatus[checkProc] = 1;
            this->currentProc = checkProc;
            this->draw();
            this->runingProc = this->procQuantum[checkProc];
            pthread_mutex_unlock(this->procStatusLock);
            return;
        }
        check ++;
    }
    this->currentProc = -1;
    this->draw();
    this->runingProc = 0;
    pthread_mutex_unlock(this->procStatusLock);
}

int Core::down(int id) {
    if (this->semaphore[id] == 0) {
        pthread_mutex_lock(this->procStatusLock);
        this->procStatus[this->currentProc] = 3;
        this->blockedProc[this->currentProc] = id;
        this->runingProc = 0;
        pthread_mutex_unlock(this->procStatusLock);
        return 0;
    } else {
        this->semaphore[id]--;
        this->drawSemaphore();
        return 1;
    }
}

int Core::up(int id) {
    int i;
    for(i = 0; i < 16; i ++) {
        if (this->blockedProc[i] == id) {
            pthread_mutex_lock(this->procStatusLock);
            this->procStatus[i] = 0;
            this->blockedProc[i] = 0;
            pthread_mutex_unlock(this->procStatusLock);
            this->drawSemaphore();
            return 1;
        }
    }
    this->semaphore[id]++;
    this->drawSemaphore();
    return 1;
}

int Core::getCurrent() {
    return this->currentProc;
}

void Core::drawSemaphore() {
    int i;
    pthread_mutex_lock(this->screen);
    for (i = 1; i < 3; i++) {
        mt_gotoXY(this->x+i,this->y+45);
        printf("семафор %d: %d   ", i, this->semaphore[i]);
    }
    printf("\n");
    pthread_mutex_unlock(this->screen);
}

void Core::drowCounter() {
    int i;
    pthread_mutex_lock(this->screen);
    mt_gotoXY(this->x+3,this->y+45);
    printf("Тактов до смены: %d  \n", this->runingProc);
    pthread_mutex_unlock(this->screen);
}

void Core::tick() {
    this->runingProc --;
    if (this->runingProc <= 0) {
        this->chooseTask();
    }
    this->drowCounter();
}

void Core::stopProc (int i) {
    pthread_mutex_lock(this->procStatusLock);
    if (this->procStatus[i] == 2) {
        this->procStatus[i] = 0;
    } else {
        this->procStatus[i] = 2;
    }
    pthread_mutex_unlock(this->procStatusLock);
}