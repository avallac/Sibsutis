#include <pthread.h>
#include "libTerm.h"

class Core: public Window
{
    private:
        pthread_mutex_t * procStatusLock;
        pthread_mutex_t * screen;
        int allProc;
        // 0 - wait
        // 1 - work
        // 2 - stop
        // 3 - lock
        int procStatus[32];
        int blockedProc[32];
        int currentProc;
        int error;
    public:
        int semaphore[32];
        int editProc;
        int runingProc;
        int procQuantum[32];
        Core(pthread_mutex_t *, int);
        void printProcStatus(int);
        void draw();
        void chooseTask();
        int down(int id);
        int up(int id);
        int getCurrent();
        void stopProc (int i);
        void drawSemaphore();
        void drowCounter();
        void tick();
        void setPosition(int a,int b, int c, int d);
        void addError();
        void drowError();
};