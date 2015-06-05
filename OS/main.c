#include "libTerm.h"
#include <sys/time.h>
#include <signal.h>
#include <unistd.h>
#include <pthread.h>


#define N 3
pthread_mutex_t screen;
pthread_mutex_t procStatusLock;
pthread_t tid[2];
// 0 - wait
// 1 - work
// 2 - stop
// 3 - lock
int procStatus[N];
int procQuantum[N];
int currentProc;
int exitFlag;
int editMode;
int editProc;
int runingProc;
int buffer;

#define EMPTY 1
#define FULL 2

int semaphore[16];
int blockedProc[16];

double startTime;
static int timerCount = 0;

void printProcStatus(int i) {
    if (editProc == i) {
        mt_setfgcolor(MT_RED);
    }
    printf("Процесс номер %d: ", i);
    mt_setfgcolor(MT_BLACK);
    if (!procQuantum[i]) {
        mt_setfgcolor(MT_RED);
        printf("нет квантов ");
    } else if (procStatus[i] == 2) {
        mt_setfgcolor(MT_RED);
        printf("остановлен  ");
    } else if (procStatus[i] == 1) {
        mt_setfgcolor(MT_GREEN);
        printf("работает    ");
    } else if (procStatus[i] == 0) {
        mt_setfgcolor(MT_YELLOW);
        printf("ожидает     ");
    } else if (procStatus[i] == 3) {
        mt_setfgcolor(MT_RED);
        printf("заблокирован");
    }
    mt_setfgcolor(MT_BLACK);
    printf(" квантов: %d      ", procQuantum[i]);
    printf("\n");
}
void drawClock(int x, int y, int h, int w) {
    struct timeval  tv;
    gettimeofday(&tv, NULL);
    double currentTime = (tv.tv_sec) * 1000 + (tv.tv_usec) / 1000 ;
    double progTime = startTime + 50 * timerCount;
    int unixTime = (int)(progTime/1000);
    pthread_mutex_lock(&screen);
    bc_box(x, y, h, w);
    mt_gotoXY(x+1,y+1);
    printf("Время: %.2i:%.2i:%.2i ", (int)((unixTime % (24*3600))/3600) + 6, (int)((unixTime % 3600) / 60), unixTime % 60);
    printf("(error %.0f ms)      \n", currentTime - progTime);
    pthread_mutex_unlock(&screen);
}
void drawProc(int x, int y, int h, int w) {
    int i;
    pthread_mutex_lock(&screen);
    bc_box(x,  y, h, w);
    for (i = 0; i < N; i++) {
        mt_gotoXY(x+i+1,y+1);
        printProcStatus(i);
    }
    printf("\n");
    pthread_mutex_unlock(&screen);
}
void drawBuff(int x, int y, int h, int w) {
    int i;
    pthread_mutex_lock(&screen);
    bc_box(x, y, h, w);
    mt_gotoXY(x+1,y+1);
    for (i = 0; i < buffer; i += 4) {
        printf("☢");
    }
    for(; i < 100; i += 4) {
        printf(" ");
    }
    printf("\n");
    pthread_mutex_unlock(&screen);
}

void reDraw() {
    int i;
    drawClock(1, 1, 2, 51);
    drawBuff(1, 53, 2, 28);
    drawProc(4, 1, N+1, 80);
    mt_gotoXY(30,1);
    for(i=1;i<3;i++){
        printf("sem%d: %d   \n", i, semaphore[i]);
    }
    printf("proc: %d\n", currentProc);
}

void chooseTask() {
    int check = 1;
    pthread_mutex_lock(&procStatusLock);
    if (currentProc == -1) currentProc = 0;
    if (procStatus[currentProc] == 1) procStatus[currentProc] = 0;
    while(check <= N) {
        if (procStatus[(currentProc + check) % N] == 0 && procQuantum[(currentProc + check) % N]) {
            procStatus[(currentProc + check) % N] = 1;
            currentProc = (currentProc + check) % N;
            pthread_mutex_unlock(&procStatusLock);
            drawProc(4, 1, N+1, 80);
            return;
        }
        check ++;
    }
    currentProc = -1;
    drawProc(4, 1, N+1, 80);
    pthread_mutex_unlock(&procStatusLock);
}

// Обработчик события от системного таймера
void timer_handler (int signum) {
    int tmp, check;
    runingProc --;
    timerCount ++;
    if (runingProc <= 0) {
         chooseTask();
         if (currentProc != -1) {
            runingProc = procQuantum[currentProc];
         }
    }
}

void stopProc (int i) {
    pthread_mutex_lock(&procStatusLock);
    if (procStatus[i] == 2) {
        procStatus[i] = 0;
    } else {
        procStatus[i] = 2;
    }
    pthread_mutex_unlock(&procStatusLock);
}

void* key_handler(void *arg) {
    char ch;
    static int i = 0;
    while(!exitFlag) {
        read (0, &ch, 1);
        if (ch == 'q') exitFlag = 1;
        if (ch == '0') stopProc(0);
        if (ch == '1') stopProc(1);
        if (ch == '2') stopProc(2);
        if (ch == '=') procQuantum[editProc]++;
        if (ch == '-') if(procQuantum[editProc]) procQuantum[editProc]--;
        if (ch == '\033') {
            read(0, &ch, 1);
            if (ch == '[') {
                read(0, &ch, 1);
                if (ch == 'A') editProc--;
                if (ch == 'B') editProc++;
                if (editProc < 0) editProc += N;
                editProc = editProc % N;
            }
        }
        if (i == 10 ) exitFlag = 1;
        i++;
        reDraw();
    }
}

// Запуск системного таймера
void enableAlarm() {
    struct itimerval timer;
    timer.it_interval.tv_sec = 0;
    timer.it_interval.tv_usec = 500000;
    timer.it_value.tv_sec = 0;
    timer.it_value.tv_usec = 500000;
    signal(SIGALRM, (void*)timer_handler);
    setitimer (ITIMER_REAL, &timer, 0);
}


int down(int id) {
    if (semaphore[id] == 0) {
        pthread_mutex_lock(&procStatusLock);
        procStatus[currentProc] = 3;
        blockedProc[currentProc] = id;
        runingProc = 0;
        pthread_mutex_unlock(&procStatusLock);
        return 0;
    } else {
        semaphore[id]--;
        return 1;
    }
}

int up(int id) {
    int i;
    for(i = 0; i < 16; i ++) {
        if (blockedProc[i] == id) {
            pthread_mutex_lock(&procStatusLock);
            procStatus[i] = 0;
            blockedProc[i] = 0;
            pthread_mutex_unlock(&procStatusLock);
            return 1;
        }
    }
    semaphore[id]++;
    return 1;
}

int main(){
    enableAlarm();
    setTermMode(1);
    mt_clrscr();

    struct timeval  tv;
    gettimeofday(&tv, NULL);
    startTime = (tv.tv_sec) * 1000 + (tv.tv_usec) / 1000 ;
    semaphore[EMPTY] = 10;

    pthread_mutex_init(&screen, NULL);
    pthread_mutex_init(&procStatusLock, NULL);
    pthread_create(&(tid[0]), NULL, &key_handler, NULL);

    int store[4];

    while(!exitFlag){
        if(procStatus[0] == 1) {
            if (store[0]) goto unlock_p0;
            if (down(EMPTY)) {
                unlock_p0: store[0] = 0;
                up(FULL);
            } else {
                store[0] = 1;
                // save state;
            }
        }
        if(procStatus[1] == 1) {
            if (store[1]) goto unlock_p1;
            if (down(FULL)) {
                unlock_p1: store[1] = 0;
                up(EMPTY);

            } else {
                store[1] = 1;
                // save state;
            }
        }
        if(procStatus[2] == 1) {
            if (store[2]) goto unlock_p2;
            if (down(FULL)) {
                unlock_p2: store[2] = 0;
                up(EMPTY);

            } else {
                store[2] = 1;
                // save state;
            }
        }
        sleep(2);
    }
    pthread_mutex_destroy(&screen);
    setTermMode(0);
}