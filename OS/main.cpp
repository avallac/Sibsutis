#include "libTerm.h"
#include "libClock.h"
#include "libCore.h"
#include "libBuffer.h"
#include "libObj.h"
#include "libApps.h"
#include "libString.h"
#include <sys/time.h>
#include <signal.h>
#include <unistd.h>
#include <pthread.h>
#include <stdlib.h>


#define N 5
pthread_mutex_t mutex_screen;
pthread_mutex_t mutex_timer;
pthread_t tid[2];

int exitFlag;

Clock * objClock;
Core * core;
Obj * firefly;
Buffer * buffer;
String * string;
int interval;

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
    if (!pthread_mutex_trylock(&mutex_timer)) {
        core->tick();
        objClock->tick();
        firefly->move();
        string->move();
        pthread_mutex_unlock(&mutex_timer);
    } else {
        core->addError();
    }
}

void *key_handler(void *arg) {
    char ch;
    while(!exitFlag) {
        read (0, &ch, 1);
        if (ch == 'q') exitFlag = 1;
        if (ch == 'z') firefly->changeSpeed(-1);
        if (ch == 'x') firefly->changeSpeed(+1);
        if (ch == 'c') string->changeSpeed(-1);
        if (ch == 'v') string->changeSpeed(+1);
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
    timer.it_interval.tv_sec = interval/1000000;
    timer.it_interval.tv_usec = interval % 1000000;
    timer.it_value.tv_sec = interval/1000000;
    timer.it_value.tv_usec = interval % 1000000;
    signal(SIGALRM, timer_handler);
    setitimer (ITIMER_REAL, &timer, 0);
}


int main(int argc, char *argv[]){
    int i;
    if (argc < 3) {
        printf("usage: main <HZ> <STRING>\n");
        return 0;
    } else {
        interval = 1000000 / atoi(argv[1]);
    }

    srand (time(NULL));
    setTermMode(1);
    mt_clrscr();
    pthread_mutex_init(&mutex_screen, NULL);
    pthread_mutex_init(&mutex_timer, NULL);
    pthread_create(&(tid[0]), NULL, &key_handler, NULL);
    objClock = new Clock(&mutex_screen, interval);
    objClock->setPosition(1, 1, 2, 77);
    core = new Core(&mutex_screen, N);
    core->setPosition(4, 1, 6, 77);
    buffer = new Buffer(&mutex_screen);
    buffer->setPosition(14, 1, 6, 12);
    firefly = new Obj(&mutex_screen);
    firefly->setPosition(21, 1, 10, 77);
    string = new String(&mutex_screen, argv[2], 76);
    string->setPosition(34, 1, 2, 77);
    core->semaphore[EMPTY] = 5;
    App * app[N];
    for (i = 0; i < N; i ++) {
        if (i % 2) {
            app[i] = new AppR1(&mutex_screen);
        } else {
            app[i] = new AppW1(&mutex_screen);
        }
        app[i]->setPosition(9 + N, (i + 1) * 13 + 1, 6, 12);
        app[i]->draw();
    }
    enableAlarm();
    while (!exitFlag) {
        if (core->getCurrent() >= 0){
            app[core->getCurrent()]->run();
        }
        sleep(2);
    }
    pthread_mutex_destroy(&mutex_screen);
    setTermMode(0);
}