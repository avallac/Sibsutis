#include "libLab3.h"

int main() {
    char str;
    int tmp,tmp2;
    printf("Test 1: clear screen\n");
    scanf("%c", &str);
    mt_clrscr();
    printf("Test 2: cursor, go to 3,3\n");
    mt_gotoXY(3,3);
    scanf("%c", &str);
    mt_clrscr();
    printf("Test 3: cursor, go to 5,3\n");
    mt_gotoXY(5,3);
    scanf("%c", &str);
    mt_clrscr();
    mt_getscreensize(&tmp, &tmp2);
    printf("Test 4: termsize %d,%d\n", tmp, tmp2);
    printf("Test 5: ");
    mt_setfgcolor(MT_RED);
    printf("b");
    mt_setfgcolor(MT_GREEN);
    printf("g");
    mt_setfgcolor(MT_YELLOW);
    printf("color\n");
    mt_setfgcolor(MT_WHITE);
    printf("Test 6: ");
    mt_setbgcolor(MT_RED);
    printf("b");
    mt_setbgcolor(MT_GREEN);
    printf("g");
    mt_setbgcolor(MT_YELLOW);
    printf("color\n");
    mt_setbgcolor(MT_BLACK);
    return 0;
}


