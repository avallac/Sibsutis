#include <pthread.h>
#include "libTerm.h"
#include <sys/time.h>

class Clock: public Window
{
    private:
        double startTime;
        pthread_mutex_t * screen;
        int timerCount;
        int interval;
    public:
        Clock(pthread_mutex_t *, int interval);
        void draw();
        void tick();
};