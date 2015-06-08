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
    protected:
        ring * head;
        pthread_mutex_t * screen;
        int blocked;
        int numProc;
        char * nameProc;
    public:
        App(pthread_mutex_t * p1): screen(p1) {
            static int procCounter = 0;
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
            this->numProc = procCounter;
            procCounter++;
        }

        void draw() {
            pthread_mutex_lock(this->screen);
            int i;
            ring * tmp;
            bc_box(6 + N, 1 + this->numProc * 13, 10, 12);
            tmp = head;
            for(i = 0; i < 9; i++) {
                mt_gotoXY(7 + N + i,2 + this->numProc * 13);
                printf("%d        ", tmp->val);
                tmp = tmp->next;
            }
            mt_gotoXY(6 + N ,2 + this->numProc * 13);
            printf(" Вывод: %d ", this->numProc);
            mt_gotoXY(16 + N,2 + this->numProc * 13);
            printf(" %s ", this->getName());
            printf("\n");
            pthread_mutex_unlock(this->screen);
        }

        void add(int val) {
            head -> val = val;
            head = head -> next;
        }

        virtual void run() {};
        virtual char * getName() { return "default"; };

};

class AppR1 : public App
{
    public:
        AppR1(pthread_mutex_t * p1) : App(p1) {}
        virtual void run() {
            if (this->blocked) goto unlock;
            if (core->down(FULL)) {
                unlock: this->blocked = 0;
                core->up(EMPTY);
                this->add(1);
                this->draw();
            } else {
                this->blocked = 1;
            }
        }
        virtual char * getName() {
            return "Читает";
        }
};
class AppW1 : public App
{
    private:
        int count;
    public:
        AppW1(pthread_mutex_t * p1) : App(p1), count(0) {}
        virtual void run() {
            if (this->blocked) goto unlock;
            this->count++;
            if (core->down(EMPTY)) {
                unlock: this->blocked = 0;
                core->up(FULL);
                this->add(this->count);
                this->draw();
            } else {
                this->blocked = 1;
            }
        }
        virtual char * getName() {
            return "Пишет";
        }
};


int main(){
    int i;
    setTermMode(1);
    mt_clrscr();
    pthread_mutex_init(&screen, NULL);
    pthread_create(&(tid[0]), NULL, &key_handler, NULL);
    objClock = new Clock(&screen);
    core = new Core(&screen);
    buffer = new Buffer(&screen);
    buffer->draw();
    core->semaphore[FULL] = 10;
    enableAlarm();
    App * app[N];
    for(i = 0; i < N; i ++) {
        if(i %2 ){
            app[i] = new AppR1(&screen);
        } else {
            app[i] = new AppW1(&screen);
        }
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