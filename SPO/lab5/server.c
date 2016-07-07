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

class Network
{
protected:
    struct sockaddr_in addr;
    int sock;
    int lost;
public:
    Network(int lost)
    {
        this->lost = lost;
        struct timeval timeout={1,0};
        sock = socket(PF_INET, SOCK_DGRAM, 0);
        setsockopt(sock,SOL_SOCKET,SO_RCVTIMEO,(char*)&timeout,sizeof(struct timeval));
        if(sock < 0) { perror("socket"); exit(1); }
        addr.sin_family = AF_INET;
        addr.sin_port = htons(3425);
        addr.sin_addr.s_addr = INADDR_ANY;
        if(bind(sock, (struct sockaddr *)&addr, sizeof(addr)) < 0) {
            perror("bind");
            exit(2);
        }
    }
    void send(char * buffer, int messageLen, sockaddr_in* client)
    {
        printf("Отправлено ");
        dumpMsg(buffer, messageLen, 0);
        ::sendto(sock, buffer, messageLen, 0, (struct sockaddr*)client, sizeof(sockaddr_in));
    }
    void dumpMsg(char * buffer, int messageLen, int lost)
    {
        if (messageLen == -1) return;
        printf("сообщение - код %i размер %i ", buffer[1], messageLen);
        if (buffer[1] != 1) {
            printf("[блок %i]", buffer[3]);
        } else {
            printf("[файл %s]", buffer + 2);
        }
        if (lost) printf(" - специально потеряно");
        printf("\n");
    }
    int recv(char * buffer, int messageLen, sockaddr_in* client)
    {
        int len = sizeof(client);
        int ret;
        while(1) {
            ret = ::recvfrom(sock, buffer, messageLen, 0, (struct sockaddr *)client, (socklen_t *)&len);
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
    ~Network()
    {
        close(sock);
    }
};

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
    void waitFile()
    {
        char buffer[1024];
        char path[128];
        int bytes_read;
        bytes_read = net->recv(buffer, 1024, &client);
        if (bytes_read != -1 && getCode(buffer) == 2) {
            printf("Входящее подключение от %s\n", inet_ntoa(client.sin_addr));
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
        do {
            bytes_read = net->recv(buffer, 1024, &client);
            if (bytes_read != -1) {
                if (getCode(buffer) == 3 && getNum(buffer) == num) {
                    lastMess = time(NULL);
                    fwrite(buffer + 4, bytes_read - 4, 1, output);
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
        net->send(buffer, size, &client);
    }
};
int main()
{
    Network * net = new Network(20);
    TFTP * client = new TFTP(net);
    while(1) {
        client->waitFile();
    }
    return 0;
}