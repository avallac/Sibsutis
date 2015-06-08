#include <pthread.h>
#include "libTerm.h"

class Core
{
    private:
        pthread_mutex_t * procStatusLock;
        pthread_mutex_t * screen;
        int x, y, h, w;
        // 0 - wait
        // 1 - work
        // 2 - stop
        // 3 - lock
        int procStatus[32];
        int blockedProc[32];
        int currentProc;
    public:
        int semaphore[32];
        int editProc;
        int runingProc;
        int procQuantum[32];
        Core(pthread_mutex_t *);
        void printProcStatus(int);
        void draw();
        void chooseTask();
        int down(int id);
        int up(int id);
        int getCurrent();
        void stopProc (int i);
        void drawSemaphore();
};