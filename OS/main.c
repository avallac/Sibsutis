#include "libTerm.h"
#include <sys/time.h>
#include <signal.h>
#include <unistd.h>
#include <pthread.h>


#define N 3
pthread_mutex_t lock;
pthread_mutex_t procStatusLock;
pthread_t tid[2];
// 0 - wait
// 1 - work
// 2 - stop
// 3 - lock
int procStatus[N];
int currentProc;
int exitFlag;
double startTime;
static int timerCount = 0;

void printProcStatus(int i) {
    printf("Процесс номер %d: ", i);
    if (procStatus[i] == 1) {
        mt_setfgcolor(MT_GREEN);
        printf("работает    ");
    } else if (procStatus[i] == 0) {
        mt_setfgcolor(MT_YELLOW);
        printf("ожидает     ");
    } else if (procStatus[i] == 2) {
        mt_setfgcolor(MT_RED);
        printf("остановлен  ");
    } else if (procStatus[i] == 3) {
        mt_setfgcolor(MT_RED);
        printf("заблокирован");
    }
    printf("\n");
    mt_setfgcolor(MT_BLACK);
}

void* reDraw(void *arg) {
    int count = 0;
    int i;
    while(1) {
        count++;
        bc_box(1,  1, N+1, 30);


        for (i = 0; i < N; i++) {
            mt_gotoXY(i+2,2);
            printProcStatus(i);
        }
        bc_box(4, 32, 2, 50);
        mt_gotoXY(5,33);
        for (i = 0; i < count; i += 5) {
            printf("☢");
        }
        printf("\n");

        struct timeval  tv;
        gettimeofday(&tv, NULL);
        double currentTime = (tv.tv_sec) * 1000 + (tv.tv_usec) / 1000 ;
        double progTime = startTime + 50 * timerCount;
        int unixTime = (int)(progTime/1000);
        bc_box(1, 32, 2, 50);
        mt_gotoXY(2,33);
        printf("Время: %i:%i:%.2i ", (int)((unixTime % (24*3600))/3600) + 6, (int)((unixTime % 3600) / 60), unixTime % 60);
        printf("(error %.0f ms)\n", currentTime - progTime);
        pthread_mutex_lock(&lock);
    }
}

void chooseTask() {
    int check = 1;
    pthread_mutex_lock(&procStatusLock);
    if (currentProc == -1) currentProc = 0;
    if (procStatus[currentProc] == 1) procStatus[currentProc] = 0;
    while(check <= N) {
        if (procStatus[(currentProc + check) % N] == 0 ) {
            procStatus[(currentProc + check) % N] = 1;
            currentProc = (currentProc + check) % N;
            pthread_mutex_unlock(&procStatusLock);
            return;
        }
        check ++;
    }
    currentProc = -1;
    pthread_mutex_unlock(&procStatusLock);
}

// Обработчик события от системного таймера
void timer_handler (int signum) {
    int tmp, check;
    timerCount ++;
    if (!(timerCount % 10)) {
         chooseTask();
         pthread_mutex_unlock(&lock);
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
    while(!exitFlag) {
        read (0, &ch, 1);
        if (ch == 'q') exitFlag = 1;
        if (ch == '0') stopProc(0);
        if (ch == '1') stopProc(1);
        if (ch == '2') stopProc(2);
    }
    pthread_mutex_unlock(&lock);
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
    setTermMode(1);
    mt_clrscr();

    struct timeval  tv;
    gettimeofday(&tv, NULL);
    startTime = (tv.tv_sec) * 1000 + (tv.tv_usec) / 1000 ;

    pthread_mutex_init(&lock, NULL);
    pthread_mutex_init(&procStatusLock, NULL);
    pthread_create(&(tid[0]), NULL, &reDraw, NULL);
    pthread_create(&(tid[1]), NULL, &key_handler, NULL);
    while(!exitFlag){
        if(procStatus[0] == 1) {

        }
        if(procStatus[1] == 1) {

        }
        sleep(1);
    }
    pthread_mutex_destroy(&lock);
    setTermMode(0);
}