#include <X11/Xlib.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <pthread.h>
#include <unistd.h>
#include <stdlib.h>
#include <fcntl.h>
#include <sys/ipc.h>
#include <sys/types.h>
#include <sys/ipc.h>
#include <sys/msg.h>
#include <stdio.h>

#define MSGSZ     128
#define W 500
#define H 500
#define M 4
#define N 14

int pipefd[N][2];

pthread_t tid[N];
pthread_mutex_t mutex;

typedef struct msgbuf {
    long    mtype;
    char    mtext[MSGSZ];
} message_buf;

int s;
GC gr_black, gr_white, gr_yellow, gr_green, gr_red, gr_tmp;
Display *d;
Window w;
int tram_pos;
int msqid;

void init()
{
    char str_yellow[] = "#FFFF00";
    char str_red[] = "#FF0000";
    char str_green[] = "#00FF00";
    XColor colour;
    Colormap colormap;
    XGCValues gr_values;
    
    d = XOpenDisplay(NULL);
    colormap = DefaultColormap(d, 0);
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
    XParseColor(d, colormap, str_yellow, &colour);
    XAllocColor(d, colormap, &colour);
    gr_values.plane_mask = AllPlanes;
    gr_values.foreground = colour.pixel;
    gr_values.background = WhitePixel(d,s);
    gr_yellow = XCreateGC(d,w, GCPlaneMask | GCForeground | GCBackground, &gr_values);
    XParseColor(d, colormap, str_red, &colour);
    XAllocColor(d, colormap, &colour);
    gr_values.plane_mask = AllPlanes;
    gr_values.foreground = colour.pixel;
    gr_values.background = WhitePixel(d,s);
    gr_red = XCreateGC(d,w, GCPlaneMask | GCForeground | GCBackground, &gr_values);
    XParseColor(d, colormap, str_green, &colour);
    XAllocColor(d, colormap, &colour);
    gr_values.plane_mask = AllPlanes;
    gr_values.foreground = colour.pixel;
    gr_values.background = WhitePixel(d,s);
    gr_green = XCreateGC(d,w, GCPlaneMask | GCForeground | GCBackground, &gr_values);
}

void *mine(void *i)
{
    char tmp[3];
    int num = *((int *) i);
    int timeToCreate = 20;
    int currentTime = 0;
    int count = 0;
    int target = 0;
    int buzy = 0;
    int old_buzy, old_count;
    message_buf sbuf, rbuf;
    sbuf.mtype = num;
    while (1) {
        usleep(10000);
        if (count == 0) {
            target = rand() % 8 + 1;
            buzy = 1;
        }
        if (buzy) {
            currentTime ++;
            if (currentTime >= timeToCreate) {
                count++;
                currentTime = 0;
            }
        }
        if (count == target && buzy) {
            sbuf.mtext[0] = 1;
            sbuf.mtext[1] = count;
            msgsnd(msqid, &sbuf, 4, IPC_NOWAIT);
            buzy = 0;
        }
        if (count != old_count || buzy != old_buzy) {
            sbuf.mtext[0] = 2;
            sbuf.mtext[1] = count;
            sbuf.mtext[2] = buzy;
            msgsnd(msqid, &sbuf, 4, IPC_NOWAIT);
            old_count = count;
            old_buzy = buzy;
        }
        if (msgrcv(msqid, &rbuf, MSGSZ, num+1, IPC_NOWAIT) != -1) {
            count--;
        }
    }
}

void *boiler(void *i)
{
    char tmp[1];
    int num = *((int *) i);
    int count = 1;
    int timeToBurn = 200;
    int currentTime = 0;
    message_buf sbuf, rbuf;
    sbuf.mtype = num;
    int old_count = -1;
    while (1) {
        usleep(10000);
        if (count > 0) {
            currentTime ++;
            if (currentTime >= timeToBurn) {
                count--;
                currentTime = 0;
            }
        }
        if (count != old_count) {
            sbuf.mtext[0] = count;
            msgsnd(msqid, &sbuf, 4, IPC_NOWAIT);
            old_count = count;
        }
        write(pipefd[num][1], tmp, 1);
        if (msgrcv(msqid, &rbuf, MSGSZ, num+1, IPC_NOWAIT) != -1) {
            count++;
        }
    }
}
void *tram(void *i)
{
    message_buf sbuf, rbuf;
    unsigned char tmp[3];
    int num = *((int *) i);
    int goTo;
    int pos = 0;
    int bloks = 0;
    int timeToLoad = 10;
    int currentTime = 0;
    int needLoad, needUpLoad;
    int wantBlocks = 0;
    int boiler;
    sbuf.mtype = num;
    while (1) {
        if (msgrcv(msqid, &rbuf, MSGSZ, num + 2, IPC_NOWAIT) != -1) {
            wantBlocks = rbuf.mtext[0];
        }
        tram_pos = pos;
        if (needLoad || needUpLoad) {
            if (wantBlocks || needUpLoad) {
                currentTime ++;
            }
            if (currentTime >= timeToLoad) {
                currentTime = 0;
                if (needUpLoad) {
                    sbuf.mtext[0] = 2;
                    sbuf.mtext[1] = boiler;
                    msgsnd(msqid, &sbuf, 4, IPC_NOWAIT);
                    bloks--;
                    if (!bloks) {
                        boiler = 0;
                        needUpLoad = 0;
                    }
                } else {
                    sbuf.mtext[0] = 3;
                    msgsnd(msqid, &sbuf, 4, IPC_NOWAIT);
                    bloks++;
                    if (bloks == wantBlocks) {
                        needLoad = 0;
                        wantBlocks = 0;
                    }
                }
            }

        } else {
            if (!goTo) {
                pos -= 30;
                if (pos <= 0) {
                    pos = 0;
                    if (msgrcv(msqid, &rbuf, MSGSZ, num + 1, IPC_NOWAIT) != -1) {
                        goTo = 120 + rbuf.mtext[0] * 70;
                        boiler = rbuf.mtext[0];
                    }
                    if (!bloks) {
                        needLoad = 1;
                    }
                }
            } else {
                pos += 15;
                if (goTo <= pos) {
                    goTo = 0;
                    needUpLoad = 1;
                }
            }
        }
        usleep(10000);
    }
}
void *disp(void *i)
{
    int count, tmp, n;
    char str[4];
    unsigned char buf[10];
    while (1) {
        count = read(pipefd[0][0], buf, 10);
        if (count > 0) {
            XClearWindow(d,w);
            if (!buf[M+4]) {
                XFillRectangle(d, w, gr_green, 30, 100, 60, 30);
            } else {
                XFillRectangle(d, w, gr_yellow, 30, 100, 60, 30);
            }
            for (tmp = 0; tmp < buf[0]; tmp++) {
                XFillRectangle(d, w, gr_black, 40, 135 + tmp * 23, 40, 20);
            }
            for (n = 0; n < M; n++) {
                if (buf[n+1]) {
                    XFillRectangle(d, w, gr_green, 150 + n * 70, 100, 60, 30);
                } else {
                    XFillRectangle(d, w, gr_red, 150 + n * 70, 100, 60, 30);
                }
                for (tmp = 0; tmp < buf[n+1]; tmp++) {
                    XFillRectangle(d, w, gr_black, 160 + n * 70, 135 + tmp * 23, 40, 20);
                }
            }
            XFillRectangle(d, w, gr_black, 30 + tram_pos, 50, 40, 20);
            sprintf(str, "%d", buf[M + 3]);
            XDrawString(d, w, gr_black, 50 + tram_pos, 47, str, strlen(str));
            XFlush(d);
        }
        usleep(10000);
    }
}

void init_pipe (int p[])
{
    pipe(p);
    fcntl(p[0], F_SETFL, O_NONBLOCK);
    fcntl(p[1], F_SETFL, O_NONBLOCK);
}

int * sendId(int i)
{
    int * arg;
    arg = (int *)malloc(sizeof(arg));
    *arg = i;
    return arg;
}
int main(int argc, char *argv[])
{
    message_buf  rbuf, sbuf;
    int msgflg = IPC_CREAT | 0666;
    key_t key;
    
    size_t buf_length;
    key = 10;
    
    if ((msqid = msgget(key, msgflg )) < 0) {
        perror("msgget");
        exit(1);
    }
    
    unsigned char tmp[10];
    unsigned char buf[3];
    int notEmpty[M];
    int * arg;
    int n;
    init();
    init_pipe(pipefd[0]);
    pthread_create(&(tid[0]), NULL, disp, NULL);
    for (n = 0; n < M; n++) {
        pthread_create(&(tid[n+1]), NULL, boiler, sendId(2 * n + 1));
    }
    pthread_create(&(tid[M * 2 + 1]), NULL, tram, sendId(M * 2 + 1));
    pthread_create(&(tid[M * 2 + 2]), NULL, mine, sendId(M * 2 + 4));
    
    
    for (n = 0; n < 10; n++) {
        tmp[n] = 0;
    }
    while (1) {
        write(pipefd[0][1], tmp, 10);
        for (n = 0; n < M; n++) {
            if (msgrcv(msqid, &rbuf, MSGSZ,  2 * n + 1, IPC_NOWAIT) != -1) {
                tmp[n + 1] = rbuf.mtext[0];
                if (!rbuf.mtext[0]) {
                    if (notEmpty[n]) {
                        sbuf.mtype = M * 2 + 2;
                        sbuf.mtext[0] = n;
                        msgsnd(msqid, &sbuf, 2, 0);
                        notEmpty[n] = 0;
                    }
                } else {
                    notEmpty[n] = 1;
                }
            }
        }
        if (msgrcv(msqid, &rbuf, MSGSZ,  M * 2 + 1, IPC_NOWAIT) != -1) {
            if (rbuf.mtext[0] == 3) {
                tmp[M + 3]++;
                sbuf.mtype = M * 2 + 5;
                msgsnd(msqid, &sbuf, 1, IPC_NOWAIT);
            } else {
                tmp[M + 3]--;
                sbuf.mtype = rbuf.mtext[1] * 2 + 2;
                msgsnd(msqid, &sbuf, 1, IPC_NOWAIT);
            }
        }
        if (msgrcv(msqid, &rbuf, MSGSZ,  M * 2 + 4, IPC_NOWAIT) != -1) {
            if (rbuf.mtext[0] == 2) {
                tmp[0] = rbuf.mtext[1];
                tmp[M + 4] = rbuf.mtext[2];
            } else {
                sbuf.mtype = M * 2 + 3;
                sbuf.mtext[0] = rbuf.mtext[1];
                msgsnd(msqid, &sbuf, 1, 0);
            }
        }
        usleep(10000);
    }
    XCloseDisplay(d);
    return 0;
}