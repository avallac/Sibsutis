#include "libObj.h"

Obj::Obj (pthread_mutex_t * p1): screen(p1) {
    posX = 1;
    posY = 1;
    moveX = 1;
    moveY = 1;
}

void Obj::draw() {
    int i;
    pthread_mutex_lock(this->screen);
    bc_box(this->x, this->y, this->h, this->w);
    printf("\n");
    pthread_mutex_unlock(this->screen);
}

void Obj::move() {
    this->draw();
    mt_gotoXY(x + posX, y + posY);
    printf(" ");
    posX += moveX;
    posY += moveY;
    if (posX >= h || posX <= 0) {
        moveX *= -1;
        posX += 2 * moveX;
    }
    if (posY >= w || posY <= 0) {
        moveY *= -1;
        posY += 2 * moveY;
    }
    mt_gotoXY(x + posX, y + posY);
    printf("🐞\n");
}