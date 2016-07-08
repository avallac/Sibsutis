#include <stdlib.h>
#include <cstdio>
#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <unistd.h> 
#include <netdb.h>
#include <memory.h>
#include <string.h>
#include <arpa/inet.h>
#include <time.h>
#include "libNetwork.h"
#include <pthread.h>

class TFTP
{
protected:
    FILE *output;
    Network * net;
    sockaddr_in client;
public:
    TFTP(Network * net)
    {
        this->net = net;
    }
    int getCode(char * buffer)
    {
        return buffer[0] * 256 + buffer[1];
    }
    
    int getNum(char * buffer)
    {
        return buffer[2] * 256 + buffer[3];
    }
    void waitFile(char * buffer)
    {
        char path[128];
        if (getCode(buffer) == 2) {
            memcpy(path, "upload/", 7);
            memcpy(path + 7, buffer + 2, strlen(buffer + 2) + 1);
            printf("Запись в файл: %s\n",path);
            output = fopen(path, "wb");
            msgAck(0);
            if (getCode(buffer) == 2) {
                downloadFile();
            }
        }
    }
    void downloadFile()
    {
        unsigned long lastMess;
        char buffer[1024];
        int bytes_read;
        int num = 1;
        lastMess = time(NULL);
        do {
            bytes_read = net->recv(buffer, 1024);
            if (bytes_read != -1) {
                if (getCode(buffer) == 3 && getNum(buffer) == num) {
                    lastMess = time(NULL);
                    fwrite(buffer + 4, bytes_read - 4, 1, output);
                    sleep(1);
                    msgAck(num++);
                } else if (getCode(buffer) == 3 && getNum(buffer) == num - 1) {
                    msgAck(num - 1);
                } else {
                    printf("-> Получено неправильное сообщение. Игнорируем\n");
                }
            } else {
                if ((time(NULL) - lastMess) > 10) {
                    printf("Подключение слишком долго не активно. Обрыв\n");
                    break;
                };
            }
        } while(bytes_read == 516 || bytes_read == -1);
        printf("Передача окончена\n");
        fclose(output);
    }
    void msgAck(int num)
    {
        char buffer[1024];
        int size = 0;
        int code = 4;
        buffer[size++] = code / 256;
        buffer[size++] = code % 256;
        buffer[size++] = num / 256;
        buffer[size++] = num % 256;
        net->send(buffer, size);
    }
};

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