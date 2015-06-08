#include "libTerm.h"
#include "libClock.h"
#include "libCore.h"
#include "libBuffer.h"
#include "libObj.h"
#include "libApps.h"
#include <sys/time.h>
#include <signal.h>
#include <unistd.h>
#include <pthread.h>
#include <stdlib.h>


#define N 5
pthread_mutex_t screen;
pthread_t tid[2];

int exitFlag;

Clock * objClock;
Core * core;
Obj * firefly;
Buffer * buffer;

class AppW1 : public App
{
    private:
        int start, count;
    public:
        AppW1(pthread_mutex_t * p1) : App(p1) {
            static int count = 0;
            start = count * 100;
            count++;
        }

        virtual void run() {
            int i;
            if (this->blocked) goto unlock_r1;
            //tmp = rand() % 100+1;
            count ++;
            tmp = start + count;
            this->add(tmp);
            this->draw();
            for (i=0; i <= 10; i++) {
                if (i*i != tmp) {
                    if (core->down(EMPTY)) {
                        unlock_r1: this->blocked = 0;
                        core->up(FULL);
                        buffer->add(tmp);
                        return;
                    } else {
                        this->blocked = 1;
                        return;
                    }
                }
            }
        }

        virtual char * getName() {
            return "Пишет";
        }
};
class AppR1 : public App
{
    public:
        AppR1(pthread_mutex_t * p1) : App(p1) {}
        virtual void run() {
            if (this->blocked) goto unlock_w1;
            if (core->down(FULL)) {
                unlock_w1: this->blocked = 0;
                core->up(EMPTY);
                this->add(buffer->get());
                this->draw();
            } else {
                this->blocked = 1;
            }
        }

        virtual char * getName() {
            return "Читает";
        }
};


void timer_handler (int signum) {
    core->tick();
    objClock->tick();
    firefly->move();
}

void *key_handler(void *arg) {
    char ch;
    while(!exitFlag) {
        read (0, &ch, 1);
        if (ch == 'q') exitFlag = 1;
        if (ch == 's') core->stopProc(core->editProc);
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


int main(){
    int i;
    srand (time(NULL));
    setTermMode(1);
    mt_clrscr();
    pthread_mutex_init(&screen, NULL);
    pthread_create(&(tid[0]), NULL, &key_handler, NULL);
    objClock = new Clock(&screen);
    objClock->setPosition(1, 1, 2, 77);
    objClock->draw();
    core = new Core(&screen, N);
    core->setPosition(4, 1, N + 1, 77);
    core->draw();
    buffer = new Buffer(&screen);
    buffer->setPosition(6 + N, 1, 6, 12);
    buffer->draw();
    firefly = new Obj(&screen);
    firefly->setPosition(13 + N, 1, 20, 77);
    firefly->draw();
    core->semaphore[EMPTY] = 5;
    enableAlarm();
    App * app[N];
    for(i = 0; i < N; i ++) {
        if(i %2 ){
            app[i] = new AppR1(&screen);
        } else {
            app[i] = new AppW1(&screen);
        }
        app[i]->setPosition(6 + N, 1, 6, 12);
        app[i]->draw();
    }
    while(!exitFlag){
        for(i = 0; i < N; i ++ ) {
            if(core->getCurrent() == i) app[i]->run();
        }
        sleep(2);
    }
    pthread_mutex_destroy(&screen);
    setTermMode(0);
}