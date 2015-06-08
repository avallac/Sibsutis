#include <pthread.h>
#include "libTerm.h"

class Buffer: public Window
{
    private:
        pthread_mutex_t * screen;
        ring * head, * tail;
        int used;
    public:
        Buffer(pthread_mutex_t *);
        void draw();
        void add(int);
        int get();
};

