#include <stdlib.h>
#include <cstdio>
#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <unistd.h>
#include <string.h>
#include "libTFTP.h"

class TCPFlow
{
protected:
    int sock;
    int status, fLen;
    char path[128];
    FILE *output;
public:
    TCPFlow(int sock)
    {
        this->sock = sock;
        status = 1;
    }
    int downloadFile()
    {
        int bytes_read;
        char buf[1024];
        if (status == 1) {
            bytes_read = recv(sock, buf, 1, 0);
            send(sock, buf, 1, 0);
            fLen = buf[0];
            status++;
            return bytes_read;
        } else if (status == 2) {
            bytes_read = recv(sock, buf, fLen, 0);
            memcpy(path, "upload/", 7);
            memcpy(path + 7, buf, fLen);
            path[fLen + 7] = 0;
            printf("[%i]Запись в файл: '%s'\n", sock, path);
            output = fopen(path, "wb");
            status++;
            return bytes_read;
        } else if (status == 3) {
            bytes_read = recv(sock, buf, 1024, 0);
            printf("[%i]Получены данные: %i байт\n", sock, bytes_read);
            if(bytes_read <= 0) return bytes_read;
            fwrite(buf, bytes_read, 1, output);
            return bytes_read;
        }
    }
    ~TCPFlow()
    {
        printf("TCP сокет закрыт\n");
        close(sock);
        fclose(output);
    }
};

int prepairToWorkTCP(int port)
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
    printf("Выделен TCP порт: %i\n", ntohs(addr.sin_port));
    return listener;
}
int prepairToWorkUDP(int port)
{
    int sock;
    struct sockaddr_in addr;
    sock = socket(PF_INET, SOCK_DGRAM, 0);
    addr.sin_family = AF_INET;
    addr.sin_port = htons(3425);
    addr.sin_addr.s_addr = INADDR_ANY;
    bind(sock, (struct sockaddr *)&addr, sizeof(addr));
    socklen_t namelen = sizeof(addr);
    getsockname(sock, (struct sockaddr *)&addr, &namelen);
    printf("Выделен UDP порт: %i\n", ntohs(addr.sin_port));
    return sock;
}

int main(int argc, char *argv[])
{
    int bytes_read;
    Network * net;
    TFTP * udp_clients[1024];
    TCPFlow * tcp_clients[1024];
    int messageLen = 1024;
    char buf[1024];
    fd_set active_fd_set, read_fd_set, udp_set, tcp_set;
    int sock_tcp, sock_udp, sock, i, port;
    if (argc > 1) {
        port = atoi(argv[1]);
    } else {
        port = 0;
    }
    sock_udp = prepairToWorkUDP(-1);
    sock_tcp = prepairToWorkTCP(port);
    FD_ZERO (&active_fd_set);
    FD_SET (sock_tcp, &active_fd_set);
    FD_SET (sock_udp, &active_fd_set);
    FD_ZERO (&udp_set);
    FD_ZERO (&tcp_set);
    while(1) {
        read_fd_set = active_fd_set;
        if (select (FD_SETSIZE, &read_fd_set, NULL, NULL, NULL) < 0) {
            perror ("select");
            exit (EXIT_FAILURE);
        }
        for (i = 0; i < FD_SETSIZE; ++i) {
            if (FD_ISSET (i, &read_fd_set)) {
                if (i == sock_tcp) {
                    sock = accept(i, NULL, NULL);
                    printf("Входящее подключение TCP\n");
                    tcp_clients[sock] = new TCPFlow(sock);
                    FD_SET (sock, &active_fd_set);
                    FD_SET (sock, &tcp_set);
                } else if (i == sock_udp) {
                    int len = sizeof(struct sockaddr);
                    struct sockaddr_in cli;
                    recvfrom(i, buf, messageLen, 0, (struct sockaddr *)&cli, (socklen_t *)&len);
                    printf("Входящее подключение UDP\n");
                    net = new Network(0, &cli, 0);
                    udp_clients[net->getSock()] = new TFTP(net);
                    udp_clients[net->getSock()]->waitFile((char *)buf);
                    FD_SET (net->getSock(), &active_fd_set);
                    FD_SET (net->getSock(), &udp_set);
                } else {
                    if (FD_ISSET (i , &udp_set)) {
                        bytes_read = udp_clients[i]->downloadFile();
                        if (bytes_read < 0) {
                            printf("UDP сокет закрыт\n");
                            close (i);
                            FD_CLR (i, &active_fd_set);
                            FD_CLR (i, &udp_set);
                        }
                    }
                    if (FD_ISSET (i , &tcp_set)) {
                        bytes_read = tcp_clients[i]->downloadFile();
                        if (bytes_read <= 0) {
                            delete tcp_clients[i];
                            FD_CLR (i, &active_fd_set);
                            FD_CLR (i, &tcp_set);
                        }
                    }
                }
            }
        }
    }
    return 0;
}