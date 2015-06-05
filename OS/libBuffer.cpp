#include "libBuffer.h"

Buffer::Buffer (pthread_mutex_t * p1): screen(p1) {
    x = 1;
    y = 53;
    h = 2;
    w = 28;
}

void Buffer::draw() {
    int i;
    pthread_mutex_lock(this->screen);
    bc_box(this->x, this->y, this->h, this->w);
    mt_gotoXY(this->x+1,this->y+1);
    for (i = 0; i < 34; i += 4) {
        printf("☢");
    }
    for(; i < 100; i += 4) {
        printf(" ");
    }
    printf("\n");
    pthread_mutex_unlock(this->screen);
}