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

void debug(char * buffer, int messageLen)
{
    FILE *output = NULL;
    output = fopen("test", "wb");
    fwrite(buffer, messageLen, 1, output);
    fclose(output);
}


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
        struct timeval timeout={2,0};
        sock = socket(PF_INET, SOCK_DGRAM, 0);
        setsockopt(sock,SOL_SOCKET,SO_RCVTIMEO,(char*)&timeout,sizeof(struct timeval));
        if(sock < 0) { perror("socket"); exit(1); }
        addr.sin_family = AF_INET;
        addr.sin_port = htons(3425);
        addr.sin_addr.s_addr = htonl(INADDR_LOOPBACK);
        if ( connect(sock, (struct sockaddr*)&addr, sizeof(addr)) != 0 )
            perror("connect 1");
    }
    void dumpMsg(char * buffer, int messageLen, int lost)
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
    void send(char * buffer, int messageLen)
    {
        printf("Отправлено ");
        dumpMsg(buffer, messageLen, 0);
        ::send(sock, buffer, messageLen, 0);
    }
    int recv(char * buffer, int messageLen)
    {
        int ret;
        while(1) {
            ret = ::recv(sock, buffer, messageLen, 0);
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
    private:
        int attemp;
        Network * net;
    public:
    TFTP(Network * net)
    {
        attemp = 0;
        this->net = net;
    }
    int getAck(int wCode)
    {
        char buffer[1024];
        attemp ++;
        if (attemp > 10) exit(1);
        int code, type;
        int recvlen = net->recv(buffer, sizeof(buffer));
        if (recvlen >= 0) {
            type = buffer[0] * 256 + buffer[1];
            code = buffer[2] * 256 + buffer[3];
            if (type != 4) {
                printf("-> Ошибка: получено сообщение другого типа\n");
            }
            if (code == wCode) {
                attemp = 0;
                return 1;
            } else {
                printf("-> Ошибка: был получен неверный код %i [ждем %i]\n", code, wCode);
                return 0;
            }
        } else {
            printf("-> Ошибка передачи!\n");
            return 0;
        }
    }
    
    void msgWRQ(char * filename)
    {
        char buffer[1024];
        int size = 0;
        int code = 2;
        char sConst[] = "octet";
        buffer[size++] = code / 256;
        buffer[size++] = code % 256;
        memcpy(buffer + 2, filename, strlen(filename)+1);
        memcpy(buffer +  strlen(filename) + 3, sConst, strlen(sConst)+1);
        size = strlen(filename) + strlen(sConst) + 2;
        do {
            net->send(buffer, size);
        } while (!getAck(0));
    }
    void msgData(int num, char * data, int len)
    {
        char buffer[1024];
        int size = 0;
        int code = 3;
        buffer[size++] = code / 256;
        buffer[size++] = code % 256;
        buffer[size++] = num / 256;
        buffer[size++] = num % 256;
        memcpy(buffer +  size, data, len);
        do {
            net->send(buffer, size + len);
        } while (!getAck(num));
    }
    void sendFile(char * filename)
    {
        FILE *input;
        int i, size;
        char fileData[512];
        printf("Отправляем файл %s\n", filename);
        input = fopen(filename, "rb");
        msgWRQ(filename);
        i = 1;
        do {
            size = fread(fileData, 1, 512, input);
            msgData(i, fileData, size);
            i ++;
        } while(size == 512);
        fclose(input);
    }
};

int main()
{
    int messageLen;
    int i;
    Network * net = new Network(20);
    TFTP * client = new TFTP(net);
    client->sendFile("client.c");
    return 0;
}
