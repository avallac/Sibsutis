#include "libString.h"
#include <sys/time.h>
#include <signal.h>
#include <unistd.h>
#include <pthread.h>
#include <stdlib.h>


String::String (pthread_mutex_t * p1, char * showString, int h): MoveObject(p1) {
    ring * tmp, *old;
    int len = strlen(showString);
    int i,j;
    j = 0;
    line = h;
    for(i = 0; i < h || len > j ; i++) {
        tmp = new ring;
        if (len > j) {
            if (showString[j] < 0) {
                tmp->val = showString[j] * 256 + showString[j + 1];
                j+=2;
            } else {
                tmp->val = showString[j];
                j+=1;
            }
        } else {
            tmp->val = ' ';
        }
        if (i == 0) {
            head = tmp;
        } else {
            old->next = tmp;
        }
        old = tmp;
    }
    old -> next = head;
}

void String::moveObj() {
    head = head -> next;
    this->draw();
}

char * String::getHelp() {
    return "Используйте C/V для ускорения и замедления\n";
}

void String::draw() {
    ring * tmp;
    int i;
    if (!pthread_mutex_trylock(this->screen)) {
        mt_gotoXY(x + 1, y + 1);
        tmp = head;
        mt_setfgcolor(MT_BLUE);
        for(i = 0; i < line; i ++) {
            if (i > line / 2) {
                mt_setfgcolor(MT_RED);
            }
            if(tmp->val < 0) {
                printf("%c%c", tmp->val / 256 , tmp->val % 256);
            } else {
                printf("%c", tmp->val);
            }
            tmp = tmp->next;
        }
        mt_setfgcolor(MT_BLACK);
        pthread_mutex_unlock(this->screen);
    }
}