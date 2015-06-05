#include "libClock.h"

Clock::Clock (pthread_mutex_t * p1): screen(p1) {
    x = 1;
    y = 1;
    h = 2;
    w = 51;
    struct timeval  tv;
    gettimeofday(&tv, NULL);
    this->startTime = (tv.tv_sec) * 1000 + (tv.tv_usec) / 1000 ;
    this->timerCount = 0;
}

void Clock::draw () {
    struct timeval  tv;
    gettimeofday(&tv, NULL);
    double currentTime = (tv.tv_sec) * 1000 + (tv.tv_usec) / 1000 ;
    double progTime = this->startTime + 500 * this->timerCount;
    int unixTime = (int)(progTime/1000);
    pthread_mutex_lock(this->screen);
    bc_box(this->x, this->y, this->h, this->w);
    mt_gotoXY(this->x+1,this->y+1);
    printf("Время: %.2i:%.2i:%.2i ", (int)((unixTime % (24*3600))/3600) + 6, (int)((unixTime % 3600) / 60), unixTime % 60);
    printf("(error %.0f ms)      \n", currentTime - progTime);
    pthread_mutex_unlock(this->screen);
}

void Clock::tick() {
    this->timerCount ++;
    this->draw();
}