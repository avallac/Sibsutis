#include <pthread.h>
#include "libTerm.h"

class Obj: public MoveObject
{
    private:
        int posX, posY, moveX, moveY;
    public:
        Obj(pthread_mutex_t *);
        void moveObj();
        void draw();
        char * getHelp();
};

