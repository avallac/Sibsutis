#include <X11/Xlib.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <pthread.h>
#include <unistd.h>
#include <stdlib.h>
#include <fcntl.h>

#define W 500
#define H 300
#define N 4

int pipefd[N][2];

pthread_t tid[N];
pthread_mutex_t mutex_screen;

int s;
GC gr_black, gr_white, gr_block;
Display *d;
Window w;

int getSpeedX(int num)
{
    int speed;
    if (num == 0) {
        speed = 30;
    } else if (num == 1) {
        speed = -20;
    } else {
        speed = 0;
    }
    return speed;
}

int getSpeedY(int num)
{
    int speed;
    if (num == 2) {
        speed = 10;
    } else if (num == 3) {
        speed = -10;
    } else {
        speed = 0;
    }
    return speed;
}


void genColor()
{
    char col[8];
    sprintf(col, "#%.2X%.2X%.2X", rand() % 255, rand() % 255, rand() % 255);
    XColor colour;
    XGCValues gr_values;
    Colormap colormap;
    colormap = DefaultColormap(d, 0);
    XParseColor(d, colormap, col, &colour);
    XAllocColor(d, colormap, &colour);
    gr_values.plane_mask = AllPlanes;
    gr_values.foreground = colour.pixel;
    gr_values.background = WhitePixel(d,s);
    gr_block = XCreateGC(d,w, GCPlaneMask | GCForeground | GCBackground, &gr_values);
}

void *movedObject(void *i) {
    unsigned char buf[3];
    int count;
    int num = *((int *) i);
    free(i);
    int speedX = getSpeedX(num);
    int speedY = getSpeedY(num);
    int posX = 0;
    int posY = 0;
    while(1) {
        count = read(pipefd[num][0], buf, 3);
        pthread_mutex_lock(&mutex_screen);
        if (num < 2) {
            XFillRectangle(d, w, gr_white, posX, (num + 1) * 50, 20, 20);
        } else {
            XFillArc(d, w, gr_white, (num - 1) * 50, posY, 20, 20, 0, 360*64);
        }
        if (count > 0) {
            if (buf[0] == 'a') {
                posX = buf[1] * 256 + buf[2];
            } else {
                posX = 0;
                posY = 0;
            }
        } else {
            if (posX > W) posX = 0;
            if (posY > H) posY = 0;
            if (posX < 0) posX = W;
            if (posY < 0) posY = H;
            posX += speedX/10;
            posY += speedY/10;
            if (num >= 2) {
                if (posY == H/4) {
                    genColor();
                }
            }
        }
        if (num < 2) {
            XFillRectangle(d, w, gr_block, posX, (num + 1) * 50, 20, 20);
        } else {
            XFillArc(d, w, gr_black, (num - 1) * 50, posY, 20, 20, 0, 360*64);
        }
        XFlush(d);
        pthread_mutex_unlock(&mutex_screen);
        usleep(100000);
    }
}


void init()
{
    XGCValues gr_values;
    d = XOpenDisplay(NULL);
    if (d == NULL) {
        fprintf(stderr, "Cannot open display\n");
        exit(1);
    }
    s = DefaultScreen(d);
    w = XCreateSimpleWindow(d, RootWindow(d, s), 10, 10, W, H, 1,
                            BlackPixel(d, s), WhitePixel(d, s));
    XSelectInput(d, w, ExposureMask | KeyPressMask);
    XMapWindow(d, w);
    gr_values.plane_mask = AllPlanes;
    gr_values.foreground = BlackPixel(d,s);
    gr_values.background = WhitePixel(d,s);
    gr_black=XCreateGC(d, w, GCForeground | GCBackground, &gr_values);
    gr_values.foreground = WhitePixel(d,s);
    gr_values.background = BlackPixel(d,s);
    gr_white=XCreateGC(d,w, GCPlaneMask | GCForeground | GCBackground, &gr_values);
    genColor();
}

int main(int argc, char *argv[])
{
    int i;
    unsigned char tmp[3];
    init();
    int commandCode, commandParam1, commandParam2;
    char msg[255];
    
    if (argc < 2) {
        printf("Нужен параметр\n");
        return 0;
    }
    pthread_mutex_init(&mutex_screen, NULL);
    for (i = 0; i < 4; i ++) {
        int * arg = (int *)malloc(sizeof(arg));
        *arg = i;
        if (pipe(pipefd[i]) == -1) {
            perror("pipe");
            exit(EXIT_FAILURE);
        }
        fcntl(pipefd[i][0], F_SETFL, O_NONBLOCK);
        fcntl(pipefd[i][1], F_SETFL, O_NONBLOCK);
        pthread_create(&(tid[i]), NULL, movedObject, arg);
    }
    pthread_mutex_lock(&mutex_screen);
    XDrawString(d, w, gr_black, 200, 10, argv[1], strlen(argv[1]));
    pthread_mutex_unlock(&mutex_screen);
    while (1) {
        printf("Подсказка:\n");
        printf("1 - Изменить X координаты квадрата.\n");
        printf("2 - Переключение окружностей в начальную точку.\n\n");
        printf("Код команды:\n");
        scanf("%d", &commandCode);
        if (commandCode == 1) {
            printf("Номер квадрата:\n");
            scanf("%d", &commandParam1);
            commandParam1 --;
            if (commandParam1 == 0 || commandParam1 == 1) {
                printf("Координаты:\n");
                scanf("%d", &commandParam2);
                tmp[0] = 'a';
                tmp[1] = commandParam2 / 256;
                tmp[2] = commandParam2 % 256;
                write(pipefd[commandParam1][1], tmp, 3);
            }
        }
        if (commandCode == 2) {
            tmp[0] = 'b';
            write(pipefd[2][1], tmp, 1);
            write(pipefd[3][1], tmp, 1);
        }
        printf("\n\n\n\n\n");
    }
    XCloseDisplay(d);
    return 0;
}