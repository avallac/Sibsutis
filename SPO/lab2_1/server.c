#include "libTFTP.h"
#include <pthread.h>


void *getFile(void *sock)
{
    int messageLen = 1024;
    char buffer[1024];
    int len = sizeof(struct sockaddr);
    struct sockaddr_in cli;
    int s = (int)sock;
    while(1) {
        recvfrom(s, buffer, messageLen, 0, (struct sockaddr *)&cli, (socklen_t *)&len);
        printf("Входящее подключение от %s\n", inet_ntoa(cli.sin_addr));
        Network * net = new Network(0, &cli, 0);
        TFTP * client = new TFTP(net);
        client->waitFile((char *)buffer);
    }
    pthread_exit(NULL);
}


int main()
{
    
    struct sockaddr_in addr;
        int sock;
    sock = socket(PF_INET, SOCK_DGRAM, 0);
    if(sock < 0) { perror("socket"); exit(1); }
    addr.sin_family = AF_INET;
    addr.sin_port = htons(3425);
    addr.sin_addr.s_addr = INADDR_ANY;
    if(bind(sock, (struct sockaddr *)&addr, sizeof(addr)) < 0) {
        perror("bind");
        exit(2);
    }
    pthread_t id;
    int i;
    for( i=0; i < 2; i++ ){
        pthread_create(&id, NULL, getFile, (void *)sock);
    }
    while(1) {
        sleep(1);
    }
    return 0;
}