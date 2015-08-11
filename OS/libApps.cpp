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

void App::setPosition(int a,int b, int c, int d) {
    Window::setPosition(a, b, c, d);
    mt_gotoXY(this->x , this->y + 1);
    printf(" Вывод: %d ", this->numProc);
    mt_gotoXY(this->x + this->h, this->y + 2);
    printf("[%s]", this->getName());
}

void App::draw() {
    int i;
    ring * tmp;
    if (!pthread_mutex_trylock(this->screen)) {
        tmp = head;
        for(i = 0; i < 5; i++) {
            mt_gotoXY(this->x + i + 1, this->y + 1);
            printf("%d        ", tmp->val);
            tmp = tmp->next;
        }
        printf("\n");
        pthread_mutex_unlock(this->screen);
    }
}

void App::add(int val) {
    head -> val = val;
    head = head -> next;
}