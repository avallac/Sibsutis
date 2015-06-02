#include "libTerm.h"

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

int mt_gotoAndPrint(int x, int y, char * str) {
    mt_gotoXY(x, y);
    printf("%s", str);
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
/*
int bc_box(int x1, int y1, int x2, int y2) - выводит на экран псевдогра-
фическую рамку, в которой левый верхний угол располагается в строке x1 и столбце
y1, а еѐ ширина и высота равна y2 столбцов и x2 строк;
*/
int bc_box(int x1, int y1, int x2, int y2) {
    int i;
    if ((x1<0)||(x2<0)||(y1<0)||(y2<0)) {
        return 2;
    }
    for (i=x1; i < (x1+x2); i++) {
        mt_gotoAndPrint(i, y1, "│");
        mt_gotoAndPrint(i, y1+y2, "│");
    }
    for (i=y1; i < (y1+y2); i++) {
        mt_gotoAndPrint(x1, i, "─");
        mt_gotoAndPrint(x1+x2, i, "─");
    }
    mt_gotoAndPrint(x1, y1, "┌");
    mt_gotoAndPrint(x1+x2, y1, "└");
    mt_gotoAndPrint(x1, y1+y2, "┐");
    mt_gotoAndPrint(x1+x2, y1+y2, "┘");
    return 0;
}

// включает/выключает курсор
int rk_cursorVisible(int flag) {
    if ((flag < 0) || (flag > 1)) return 1;
    if (flag)
        printf("\033[?12;25h");
    else
        printf("\033[?25l");
}

void setTermMode (int mode) {
    static struct termios savetty;
    static struct termios tty;
    if (mode == 1) {
        tcgetattr (0, &tty);
        savetty = tty;
        tty.c_lflag &= ~(ICANON|ECHO|ISIG);
        tty.c_cc[VMIN] = 1;
        tcsetattr (0, TCSAFLUSH, &tty);
        rk_cursorVisible(0);
    } else {
        tcsetattr (0, TCSAFLUSH, &savetty);
        rk_cursorVisible(1);
    }
}