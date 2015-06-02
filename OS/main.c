#include "libTerm.h"
#include <sys/time.h>
#include <signal.h>
#include <unistd.h>
#include <pthread.h>

#define N 3
pthread_mutex_t lock;
pthread_t tid[2];
// 0 - wait
// 1 - work
// 2 - stop
// 3 - lock
int procStatus[N];
int currentProc;

void printProcStatus(int i) {
    printf("Proc%d: ", i);
    if (procStatus[i] == 1) {
        mt_setfgcolor(MT_GREEN);
        printf("work");
    } else if (procStatus[i] == 0) {
        mt_setfgcolor(MT_YELLOW);
        printf("wait");
    } else if (procStatus[i] == 2) {
        mt_setfgcolor(MT_RED);
        printf("stop");
    } else if (procStatus[i] == 3) {
        mt_setfgcolor(MT_RED);
        printf("lock");
    }
    mt_setfgcolor(MT_BLACK);
}

void* reDraw(void *arg) {
    int count = 0;
    int i;
    while(1) {
        count++;
        for(i=0; i < N; i++) {
            mt_gotoXY(i+1,20);
            printProcStatus(i);
        }
        mt_gotoXY(N+1,20);
        printf("act: %d\n", count);
        pthread_mutex_lock(&lock);
    }
}

void chooseTask() {
    int check = 1;
    if (currentProc == -1) currentProc = 0;
    procStatus[currentProc] = 0;
    while(check < N) {
        if (procStatus[(currentProc + check) % N] == 0 ) {
            procStatus[(currentProc + check) % N] = 1;
            currentProc = (currentProc + check) % N;
            return;
        }
        check ++;
    }
    currentProc = -1;
}

// Обработчик события от системного таймера
void timer_handler (int signum) {
    int tmp, check;
    static int count = 0;
    static int next = 0;
    static int old = 0;
    count ++;
    if (!(count % 10)) {
         chooseTask();
         pthread_mutex_unlock(&lock);
    }
}

// Запуск системного таймера
void enableAlarm() {
    struct itimerval timer;
    timer.it_interval.tv_sec = 0;
    timer.it_interval.tv_usec = 50000;
    timer.it_value.tv_sec = 0;
    timer.it_value.tv_usec = 50000;
    signal(SIGALRM, (void*)timer_handler);
    setitimer (ITIMER_REAL, &timer, 0);
}

int main(){
    enableAlarm();
    mt_clrscr();
    bc_box(1, 1, 20, 20);
    procStatus[0] = 1;
    procStatus[1] = 0;

    if (pthread_mutex_init(&lock, NULL) != 0) {
            printf("\n mutex init failed\n");
            return 1;
    }

    pthread_create(&(tid[0]), NULL, &reDraw, NULL);

    while(1){
        if(procStatus[0] == 1) {

        }
        if(procStatus[1] == 1) {

        }
        sleep(1);
    }

    pthread_mutex_destroy(&lock);
}