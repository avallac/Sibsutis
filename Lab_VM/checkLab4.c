#include "libLab4.h"
#include <sys/stat.h> 
#include <fcntl.h>

int main() {
    int font[2][2];
    int i, k, tmp;
    char str;
    mt_clrscr();
    bc_box(1, 1, 10, 10);
    scanf("%c", &str);
    mt_clrscr();
    int fd = open("font.bc", O_RDONLY);
    for(i = 0; i < 10; i++) {
        bc_bigcharread(fd, font[0], 1, &tmp);
        bc_printbigchar(font[0], 1 + i*8, 1, MT_WHITE, MT_BLACK);
    }
    scanf("%c", &str);
    mt_clrscr();
    font[1][0] = 0; font[1][1] = 0;
    for(i = 0; i < 8; i++) {
        bc_setbigcharpos(font[1], i, i, 1);
    }
    bc_printbigchar(font[1], 1, 1, MT_WHITE, MT_BLACK);
    printf("\n");
    bc_getbigcharpos(font[1], 0, 0, &tmp);
    printf("%d-%d = %d\n", 0, 0, tmp);
    bc_getbigcharpos(font[1], 1, 1, &tmp);
    printf("%d-%d = %d\n", 1, 1, tmp);
    bc_getbigcharpos(font[1], 0, 1, &tmp);
    printf("%d-%d = %d\n", 0, 1, tmp);
    close(fd);
    fd = open("font.tmp", O_WRONLY);
    bc_bigcharwrite(fd, font[1], 1);
    close(fd);
    fd = open("font.tmp", O_WRONLY);
    bc_bigcharread(fd, font[0], 1, &tmp);
    close(fd);
    bc_printbigchar(font[1], 1, 9, MT_WHITE, MT_BLACK);
    scanf("%c", &str);
    return 0;
}

