#include <pthread.h>
#include "libTerm.h"

#define EMPTY 1
#define FULL 2

class App: public Window
{
    protected:
        ring * head;
        pthread_mutex_t * screen;
        int blocked;
        int numProc;
        int tmp;
        char * nameProc;
    public:
        App(pthread_mutex_t * p1);
        void draw();
        void add(int val);
        virtual void run() {};
        virtual char * getName() { return "default"; };
        virtual void setPosition(int a,int b, int c, int d);
};
