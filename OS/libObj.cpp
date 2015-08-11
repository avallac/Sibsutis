#include "libObj.h"

Obj::Obj (pthread_mutex_t * p1): MoveObject(p1) {
    posX = 1;
    posY = 1;
    moveX = 1;
    moveY = 1;
}

char * Obj::getHelp() {
    return "Используйте Z/X для ускорения и замедления\n";
}

void Obj::moveObj() {
    if (!pthread_mutex_trylock(this->screen)) {
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
        pthread_mutex_unlock(this->screen);
    }
    this->draw();
}

void Obj::draw() {
    if (!pthread_mutex_trylock(this->screen)) {
        mt_gotoXY(x + posX, y + posY);
        printf("🐞\n");
        pthread_mutex_unlock(this->screen);
    }
}