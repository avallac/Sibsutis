#include "libApps.h"

App::App(pthread_mutex_t * p1): screen(p1) {
    static int procCounter = 0;
    int i;
    ring * tmp, *old;
    for(i = 0; i < 5; i++) {
        tmp = new ring;
        tmp->val = 0;
        if (i == 0) {
            head = tmp;
        } else {
            old->next = tmp;
        }
        old = tmp;
    }
    old -> next = head;
    this->numProc = procCounter;
    procCounter++;
    blocked = 0;
}

void App::draw() {
    int i;
    ring * tmp;
    pthread_mutex_lock(this->screen);
    bc_box(this->x, this->y + (this->numProc+1) * (this->w+1), this->h, this->w);
    tmp = head;
    for(i = 0; i < 5; i++) {
        mt_gotoXY(this->x + i + 1, this->y + 1 + (this->numProc+1) * (this->w+1));
        printf("%d        ", tmp->val);
        tmp = tmp->next;
    }
    mt_gotoXY(this->x , this->y + 1 + (this->numProc+1) * (this->w+1));
    printf(" Вывод: %d ", this->numProc);
    mt_gotoXY(this->x + this->h, this->y + 2 + (this->numProc+1) * (this->w+1));
    printf("[%s]", this->getName());
    printf("\n");
    pthread_mutex_unlock(this->screen);
}

void App::add(int val) {
    head -> val = val;
    head = head -> next;
}