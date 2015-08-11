#include <pthread.h>
#include <cstring>
#include "libTerm.h"

class String: public MoveObject
{
    private:
        int pos, moveX;
        ring * head;
        int line;
    public:
        String(pthread_mutex_t *, char *, int);
        void moveObj();
        void draw();
};

