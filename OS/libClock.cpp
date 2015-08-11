#include "libClock.h"

Clock::Clock (pthread_mutex_t * p1, int interval): screen(p1) {
    struct timeval  tv;
    gettimeofday(&tv, NULL);
    this->startTime = (tv.tv_sec) * 1000 + (tv.tv_usec) / 1000 ;
    this->timerCount = 0;
    this->interval = interval;
}

void Clock::draw () {
    struct timeval  tv;
    gettimeofday(&tv, NULL);
    double currentTime = (tv.tv_sec) * 1000 + (tv.tv_usec) / 1000 ;
    double progTime = this->startTime + this->interval * this->timerCount / 1000;
    int unixTime = (int)(progTime/1000);
    if (!pthread_mutex_trylock(this->screen)) {
        mt_gotoXY(this->x+1,this->y+1);
        printf("Время: %.2i:%.2i:%.2i ", (int)(((unixTime + 6 * 3600) % (24*3600))/3600), (int)((unixTime % 3600) / 60), unixTime % 60);
        printf("(error %.0f ms)      \n", currentTime - progTime);
        pthread_mutex_unlock(this->screen);
    }
}

void Clock::tick() {
    this->timerCount ++;
    this->draw();
}