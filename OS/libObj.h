#include <pthread.h>
#include "libTerm.h"

class Obj: public Window
{
    private:
        int posX, posY, moveX, moveY;
        pthread_mutex_t * screen;
    public:
        Obj(pthread_mutex_t *);
        void draw();
        void move();

};

