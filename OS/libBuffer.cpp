#include "libBuffer.h"

Buffer::Buffer (pthread_mutex_t * p1): screen(p1) {
    ring * tmp, *old;
    int i;
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
    tail = head;
}

void Buffer::add(int val) {
    head -> val = val;
    head = head -> next;
    used ++;
    this->draw();
}

int Buffer::get() {
    int tmp;
    tmp = tail->val;
    tail = tail -> next;
    used --;
    this->draw();
    return tmp;
}

void Buffer::setPosition(int a,int b, int c, int d) {
    Window::setPosition(a, b, c, d);
    if (!pthread_mutex_trylock(this->screen)) {
        mt_gotoXY(this->x, this->y+1);
        printf(" Буфер: ");
        pthread_mutex_unlock(this->screen);
    }
}

void Buffer::draw() {
    int i;
    ring * tmp = tail;
    if (!pthread_mutex_trylock(this->screen)) {
        i = 0;
        while (i < used) {
            mt_gotoXY(this->x + 1 + i, 2);
            printf("%d      ", tmp->val);
            tmp = tmp->next;
            i++;
        }
        while (i < 5) {
            mt_gotoXY(this->x + 1 + i, 2);
            printf("      ");
            i++;
        }
        mt_gotoXY(this->x + 6,this->y + 2);
        printf("[");
        for (i = 0; i < used; i ++) {
            printf("☢");
        }
        for(; i < 5; i ++) {
            printf(" ");
        }
        printf("]\n");
        pthread_mutex_unlock(this->screen);
    }
}