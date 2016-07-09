#include <stdlib.h>
#include <cstdio>
#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <unistd.h>
#include <string.h>

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

int main(int argc, char *argv[])
{
    char buf[1024];
    fd_set active_fd_set, read_fd_set;
    int sock, listener, i, port;
    if (argc > 1) {
        port = atoi(argv[1]);
    } else {
        port = 0;
    }
    listener = prepairToWork(port);
    FD_ZERO (&active_fd_set);
    FD_SET (listener, &active_fd_set);
    while(1) {
        read_fd_set = active_fd_set;
        if (select (FD_SETSIZE, &read_fd_set, NULL, NULL, NULL) < 0) {
            perror ("select");
            exit (EXIT_FAILURE);
        }
        for (i = 0; i < FD_SETSIZE; ++i) {
            if (FD_ISSET (i, &read_fd_set)) {
                if (i == listener) {
                    sock = accept(listener, NULL, NULL);
                    if(sock < 0) {
                        perror("Ошибка: accept");
                        exit(3);
                    }
                    FD_SET (sock, &active_fd_set);
                } else {
                    int bytes_read = recv(i, buf, 1, 0);
                    if (bytes_read < 0) {
                        close (i);
                        FD_CLR (i, &active_fd_set);
                    } else {
                        printf("%i\n", buf[0] - '0');
                    }
                }
            }
        }
    }
    return 0;
}