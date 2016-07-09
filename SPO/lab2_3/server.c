#include <stdlib.h>
#include <cstdio>
#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <unistd.h> 
#include <string.h>
#include <sys/signal.h>
#include <sys/wait.h>
#include <sys/time.h>
#include <sys/resource.h>
#include <pthread.h>

int prepairToWork(int port)
{
    struct sockaddr_in addr;
    int listener = socket(AF_INET, SOCK_STREAM, 0);
    if(listener < 0){
        perror("Ошибка: socket");
        exit(1);
    }
    addr.sin_family = AF_INET;
    addr.sin_port = htons(port);
    addr.sin_addr.s_addr = htonl(INADDR_ANY);
    if(bind(listener, (struct sockaddr *)&addr, sizeof(addr)) < 0) {
        perror("Ошибка: bind");
        exit(2);
    }
    listen(listener, 1);
    socklen_t namelen = sizeof(addr);
    if (getsockname(listener, (struct sockaddr *)&addr, &namelen) < 0){
        exit(1);
    }
    printf("Выделен порт: %i\n", ntohs(addr.sin_port));
    return listener;
}

void handleRequest(void *sock)
{
    FILE *output;
    pthread_t pid = pthread_self();
    char buf[1024], path[128];
    int bytes_read;
    recv(sock, buf, 1, 0);
    send(sock, buf, 1, 0);
    int fLen = buf[0];
    recv(sock, buf, fLen, 0);
    buf[fLen] = 0;
    memcpy(path, "upload/", 7);
    memcpy(path + 7, buf, fLen);
    printf("[%d]Запись в файл: %s\n", pid, path);
    output = fopen(path, "wb");
    while(1) {
        bytes_read = recv((int)sock, buf, 1024, 0);
        printf("[%d]Получены данные: %i байт\n", pid, bytes_read);
        if(bytes_read <= 0) break;
        fwrite(buf, bytes_read, 1, output);
    }
    close(sock);
    fclose(output);
    pthread_exit(NULL);
}
void reaper(int sig)
{
    int status;
    while(wait3(&status,WNOHANG,(struct rusage *)0)>= 0);
}

int main(int argc, char *argv[])
{
    int sock, listener, i, port;
    if (argc > 1) {
        port = atoi(argv[1]);
    } else {
        port = 0;
    }
    signal(SIGCHLD,reaper);
    listener = prepairToWork(port);
    pthread_t id;
    while(1)
    {
        sock = accept(listener, NULL, NULL);
        if(sock < 0) {
            perror("Ошибка: accept");
            exit(3);
        }
        pthread_create(&id, NULL, handleRequest, (void *)sock);
    }
    return 0;
}