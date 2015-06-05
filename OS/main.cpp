#include "libTerm.h"
#include "libClock.h"
#include "libCore.h"
#include "libBuffer.h"
#include <sys/time.h>
#include <signal.h>
#include <unistd.h>
#include <pthread.h>


#define N 3
pthread_mutex_t screen;
pthread_mutex_t procStatusLock;
pthread_t tid[2];

int exitFlag;

Clock * objClock;
Core * core;
Buffer * buffer;

#define EMPTY 1
#define FULL 2

void timer_handler (int signum) {
    int tmp, check;
    core->runingProc --;
    if (core->runingProc <= 0) {
        core->chooseTask();
    }
    objClock->tick();
}

void *key_handler(void *arg) {
    char ch;
    while(!exitFlag) {
        read (0, &ch, 1);
        if (ch == 'q') exitFlag = 1;
        if (ch == '0') core->stopProc(0);
        if (ch == '1') core->stopProc(1);
        if (ch == '2') core->stopProc(2);
        if (ch == '=') core->procQuantum[core->editProc]++;
        if (ch == '-') if(core->procQuantum[core->editProc]) core->procQuantum[core->editProc]--;
        if (ch == '\033') {
            read(0, &ch, 1);
            if (ch == '[') {
                read(0, &ch, 1);
                if (ch == 'A') core->editProc--;
                if (ch == 'B') core->editProc++;
                if (core->editProc < 0) core->editProc += N;
                core->editProc = core->editProc % N;
            }
        }
        core->draw();
    }
    return NULL;
}

void enableAlarm() {
    struct itimerval timer;
    timer.it_interval.tv_sec = 0;
    timer.it_interval.tv_usec = 500000;
    timer.it_value.tv_sec = 0;
    timer.it_value.tv_usec = 500000;
    signal(SIGALRM, timer_handler);
    setitimer (ITIMER_REAL, &timer, 0);
}

struct ring {
	int val;
	struct ring *next;
};

class App
{
    private:
        ring * head;
        pthread_mutex_t * screen;
    public:
        App(pthread_mutex_t * p1): screen(p1) {
            int i;
            ring * tmp, *old;
            for(i = 0; i < 9; i++) {
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
        }
        void draw() {
            pthread_mutex_lock(this->screen);
            int i;
            ring * tmp;
            bc_box(15, 1, 10, 20);

            tmp = head;
            for(i = 0; i < 9; i++) {
                mt_gotoXY(16+i,2);
                printf("%d        ", tmp->val);
                tmp = tmp->next;
            }
            printf("\n");
            pthread_mutex_unlock(this->screen);
        }

        void add(int val) {
            head -> val = val;
            head = head -> next;
        }
};


int main(){
    setTermMode(1);
    mt_clrscr();

    pthread_mutex_init(&screen, NULL);
    pthread_mutex_init(&procStatusLock, NULL);
    pthread_create(&(tid[0]), NULL, &key_handler, NULL);

    objClock = new Clock(&screen);
    core = new Core(&procStatusLock, &screen);
    buffer = new Buffer(&screen);
    buffer->draw();
    core->semaphore[FULL] = 10;
    enableAlarm();
    int store[4];
    int tmp[4];
    App * app = new App(&screen);

    while(!exitFlag){
        if(core->getCurrent() == 0) {
            if (store[0]) goto unlock_p0;
            if (core->down(EMPTY)) {
                unlock_p0: store[0] = 0;
                core->up(FULL);
            } else {
                store[0] = 1;
                // save state;
            }
        }
        if(core->getCurrent() == 1) {
            if (store[1]) goto unlock_p1;
            tmp[1]++;
            if (core->down(FULL)) {
                unlock_p1: store[1] = 0;
                core->up(EMPTY);
                app->add(tmp[1]);
                app->draw();

            } else {
                store[1] = 1;
                // save state;
            }
        }
        if(core->getCurrent() == 2) {
            if (store[2]) goto unlock_p2;
            if (core->down(FULL)) {
                unlock_p2: store[2] = 0;
                core->up(EMPTY);

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