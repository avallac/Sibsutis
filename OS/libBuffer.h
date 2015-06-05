#include <pthread.h>
#include "libTerm.h"

class Buffer
{
    private:
        int x, y, h, w;
        pthread_mutex_t * screen;
    public:
        Buffer(pthread_mutex_t *);
        void draw();
};