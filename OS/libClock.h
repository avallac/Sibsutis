#include <pthread.h>
#include "libTerm.h"
#include <sys/time.h>

class Clock: public Window
{
    private:
        double startTime;
        pthread_mutex_t * screen;
        int timerCount;
    public:
        Clock(pthread_mutex_t *);
        void draw();
        void tick();
};