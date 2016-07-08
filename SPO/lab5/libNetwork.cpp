#include "libNetwork.h"

Network::Network(int lost, sockaddr_in* peer, int needBind)
{
    this->peer = peer;
    this->lost = lost;
    struct timeval timeout={2,0};
    sock = socket(PF_INET, SOCK_DGRAM, 0);
    if(sock < 0) { perror("socket"); exit(1); }
    setsockopt(sock,SOL_SOCKET,SO_RCVTIMEO,(char*)&timeout,sizeof(struct timeval));
    if (needBind) {
        sockaddr_in local;
        local.sin_family = AF_INET;
        local.sin_port = htons(0);
        local.sin_addr.s_addr = INADDR_ANY;
        if(bind(sock, (struct sockaddr *)&local, sizeof(local)) < 0) {
            perror("bind");
            exit(2);
        }
    }
}
void Network::dumpMsg(char * buffer, int messageLen, int lost)
{
    if (messageLen == -1) return;
    printf("сообщение - код %i размер %i ", buffer[1], messageLen);
    if (buffer[1] != 1 && buffer[1] != 2) {
        printf("[блок %i]", buffer[3]);
    } else {
        printf("[файл %s]", buffer + 2);
    }
    if (lost) printf(" - специально потеряно");
    printf("\n");
}
void Network::send(char * buffer, int messageLen)
{
    printf("Отправлено ");
    dumpMsg(buffer, messageLen, 0);
    sendto(sock, buffer, messageLen, 0, (struct sockaddr *)peer, sizeof(sockaddr_in));
}
int Network::recv(char * buffer, int messageLen)
{
    int len = sizeof(sockaddr_in);
    int ret;
    while(1) {
        ret = ::recvfrom(sock, buffer, messageLen, 0, (struct sockaddr *)peer, (socklen_t *)&len);
        if (ret != -1 ) {
            printf("Принято ");
            if ((rand() % 100) < lost) {
                dumpMsg(buffer, ret, 1);
            } else {
                dumpMsg(buffer, ret, 0);
                return ret;
            }
        } else {
            return ret;
        }
    }
}
Network::~Network()
{
    close(sock);
}