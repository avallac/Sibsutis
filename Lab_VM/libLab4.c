#include "libLab4.h"

/*
int bc_printA (char * str) - выводит строку символов с использованием допол-
нительной кодировочной таблицы;
*/
int bc_printA (char * str) {
    printf(str);
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
        mt_gotoXY(i, y1);
        bc_printA("│");
        mt_gotoXY(i, y1+y2);
        bc_printA("│");
    }
    for (i=y1; i < (y1+y2); i++) {
        mt_gotoXY(x1, i);
        bc_printA("─");
        mt_gotoXY(x1+x2, i);
        bc_printA("─");
    }
    mt_gotoXY(x1, y1);
    bc_printA("┌");
    mt_gotoXY(x1+x2, y1);
    bc_printA("└");
    mt_gotoXY(x1, y1+y2);
    bc_printA("┐");
    mt_gotoXY(x1+x2, y1+y2);
    bc_printA("┘");
    return 0;
}

/*
int bc_printbigchar (int [2], int x, int y, enum color, enum color) -
выводит на экран "большой символ" размером восемь строк на восемь столбцов, левый
верхний угол которого располагается в строке x и столбце y. Третий и четвѐртый па-
раметры определяют цвет и фон выводимых символов. "Символ" выводится исходя из
значений массива целых чисел следующим образом. В первой строке выводится 8
младших бит первого числа, во второй следующие 8, в третьей и 4 следующие. В 5
строке выводятся 8 младших бит второго числа и т.д. При этом если значение бита = 0,
то выводится символ "пробел", иначе - символ, закрашивающий знакоместо
(ACS_CKBOARD);
*/
int bc_printbigchar (int big[2], int y, int x, enum colors color1, enum colors color2) {
    int i, j, k;
    int test;
    mt_setfgcolor (color1);
    mt_setbgcolor (color2);
    for (k = 0; k < 2; k++) {
        test = big[k];
        for (i=0; i < 4; i++) {
            for (j=0; j < 8; j++) {
                mt_gotoXY(k*4+i+x,j+y);
                if (test & 2147483648) {
                    bc_printA ("█");
                } else {
                    bc_printA (" ");
                }
                test = test << 1;
            }
        }
    }
    return 0;
}

/*
int bc_setbigcharpos (int * big, int x, int y, int value) - устанавли-
вает значение знакоместа "большого символа" в строке x и столбце y в значение value;
*/
int bc_setbigcharpos (int * big, int x, int y, int value) {
    big[y/4] |= (!!value << (31 - ( (y%4) * 8 + x)));
}

/*
int bc_getbigcharpos(int * big, int x, int y, int *value) - возвращает
значение позиции в "большом символе" в строке x и столбце y;
*/
int bc_getbigcharpos (int * big, int x, int y, int *value) {
     *value = (big[y/4] >> (31 - ((y%4) * 8 + x))) & 1;
}

/*
int bc_bigcharwrite (int fd, int * big, int count) - записывает заданное
число "больших символов" в файл. Формат записи определяется пользователем;
*/
int bc_bigcharwrite (int fd, int * big, int count){
	int i, j;
	for(j=0; j < count; j++) {
	    for(i=0; i < 2; i++) {
    		write(fd, &big[i], sizeof(int));
        }
    }
	return 0;
}

/*
int bc_bigcharread (int fd, int * big, int need_count, int * count)
считывает из файла заданное количество "больших символов". Третий параметр ука-
зывает адрес переменной, в которую помещается количество считанных символов или
0, в случае ошибки.
*/
int bc_bigcharread (int fd, int * big, int need_count, int *count) {
    int i, j;
    *count = 0;
    for(i = 0; i < 2; i++) {
        read(fd, &big[i], sizeof(int));
    }
    *count++;
    return 0;
}
