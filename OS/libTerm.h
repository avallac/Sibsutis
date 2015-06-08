#ifndef libTerm_h
#define libTerm_h
#include <stdio.h>
#include <sys/ioctl.h>
#include <fcntl.h>
#include <termios.h>
#include <pthread.h>

enum colors {
        MT_BLACK,
        MT_RED,
        MT_GREEN,
        MT_YELLOW,
        MT_BLUE,
        MT_VIOLET,
        MT_BIRUZ,
        MT_WHITE
};

struct ring {
	int val;
	struct ring *next;
};

int mt_clrscr (void);
int mt_gotoXY (int, int);
int mt_getscreensize(int *, int *);
int mt_setfgcolor (enum colors);
int mt_setbgcolor (enum colors);
int mt_gotoAndPrint(int, int, char *);
int bc_box(int x1, int y1, int x2, int y2);
int rk_cursorVisible(int flag);
void setTermMode (int mode);

class Window
{
    protected:
        int x, y, h, w;
    public:
        void setPosition(int a,int b, int c, int d);

};
#endif
