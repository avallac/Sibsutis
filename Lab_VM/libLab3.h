#include <stdio.h>
#include <sys/ioctl.h>

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

int mt_clrscr (void);