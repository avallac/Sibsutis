#include <stdlib.h>
#include <cstdio>
#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <unistd.h> 

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

int searchEndWord(char * buf, int bytes_read, int pos)
{
    for (;pos <  bytes_read;pos++) {
        if ((buf[pos] == ' ') || (buf[pos] == ',') || (buf[pos] == '.') || (buf[pos] == 0)) return pos;
    }
    return bytes_read;
}

void invert(char * buf, int bytes_read)
{
    int pos = 0;
    int end, i;
    char tmp;
    while ((end = searchEndWord(buf, bytes_read, pos)) < bytes_read) {
        for(i = 0; (end - pos)/2 > i; i++) {
            tmp = buf[pos + i];
            buf[pos + i] = buf[end - (i + 1)];
            buf[end - (i + 1)] = tmp;        }
        pos = end + 1;
    }
}

int main(int argc, char *argv[])
{
    int sock, listener, i, port;
    char buf[1024];
    int bytes_read;
    if (argc > 1) {
        port = atoi(argv[1]);
    } else {
        port = 0;
    }
    listener = prepairToWork(port);
    while(1)
    {
        sock = accept(listener, NULL, NULL);
        if(sock < 0) {
            perror("Ошибка: accept");
            exit(3);
        }
        while(1) {
            bytes_read = recv(sock, buf, 1024, 0);
            if(bytes_read <= 0) break;
            printf("Получены данные:\n -> байт: %i\n ->данные: %s\n", bytes_read, buf);
            invert(buf, bytes_read);
            send(sock, buf, bytes_read, 0);
        }
        close(sock);
    }
    
    return 0;
}