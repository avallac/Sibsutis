#include <stdio.h>
#include "libLab3.h"

int bc_printA (char * str);
int bc_box(int x1, int y1, int x2, int y2);
int bc_printbigchar (int big[2], int y, int x, enum colors color1, enum colors color2);
int bc_bigcharread (int fd, int * big, int need_count, int *count);