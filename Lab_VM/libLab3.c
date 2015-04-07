#include "libLab3.h"
/*
int mt_clrscr (void)- производит очистку и перемещение курсора в левый верх-
ний угол экрана;
*/
int mt_clrscr (void) {
    printf("\033[H\033[H\033[2J");
    return 0;
}

/*
int mt_gotoXY (int, int) - перемещает курсор в указанную позицию. Первый
параметр номер строки, второй - номер столбца;
*/
int mt_gotoXY (int x, int y) {
    printf("\033[%d;%dH", x, y);
    return 0;
}

/*
int mt_getscreensize (int * rows, int * cols) - определяет размер экрана
терминала (количество строк и столбцов);
*/
int mt_getscreensize(int *rows, int *cols)
{
    struct winsize ws;
    ioctl(1, TIOCGWINSZ, &ws);
    *rows=ws.ws_row;
    *cols=ws.ws_col;
    return 0;
}

/*
int mt_setfgcolor (enum colors) - устанавливает цвет последующих выводимых
символов. В качестве параметра передаѐтся константа из созданного Вами перечисли-
мого типа colors, описывающего цвета терминала;
*/
int mt_setfgcolor (enum colors color) {
    printf("\033[3%dm", color);
    return 0;
}

/*
int mt_setfgcolor (enum colors) - устанавливает цвет последующих выводимых
символов. В качестве параметра передаѐтся константа из созданного Вами перечисли-
мого типа colors, описывающего цвета терминала;
*/
int mt_setbgcolor (enum colors color) {
    printf("\033[4%dm", color);
    return 0;
}
