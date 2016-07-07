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

class Network
{
protected:
    struct sockaddr_in addr;
    int sock;
public:
    Network()
    {
        sock = socket(PF_INET, SOCK_DGRAM, 0);
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
        printf("Отправлено сообщение - код %i\n", buffer[1]);
        ::sendto(sock, buffer, messageLen, 0, (struct sockaddr*)client, sizeof(sockaddr_in));
    }
    int recv(char * buffer, int messageLen, sockaddr_in* client)
    {
        int len = sizeof(client);
        int ret;
        //do {
            ret = ::recvfrom(sock, buffer, messageLen, 0, (struct sockaddr *)client, (socklen_t *)&len);
        //} while(rand() % 2);
        return ret;
    }
    ~Network()
    {
        close(sock);
    }
};

int getCode(char * buffer)
{
    return buffer[0] * 256 + buffer[1];
}

int getNum(char * buffer)
{
    return buffer[2] * 256 + buffer[3];
}
int main()
{
    char path[128];
    char buffer[1024];
    struct sockaddr_in addr;
    int bytes_read;
    sockaddr_in client;
    Network * net = new Network();
    while(1) {
        bytes_read = net->recv(buffer, 1024, &client);
        memcpy(path, "upload/", 7);
        memcpy(path + 7, buffer + 2, strlen(buffer + 2) + 1);
        printf("new path: %s\n",path);
        FILE *output = NULL;
        output = fopen(path, "wb");
        buffer[0] = 0;
        buffer[1] = 4;
        buffer[2] = 0;
        buffer[3] = 0;
        net->send(buffer, 4, &client);
        int num = 1;
        do {
            bytes_read = net->recv(buffer, 1024, &client);
            if (getCode(buffer) == 3 && getNum(buffer) == num) {
                fwrite(buffer + 4, bytes_read - 4, 1, output);
                buffer[0] = 0;
                buffer[1] = 4;
                buffer[2] = num / 256;
                buffer[3] = num % 256;
                num++;
                net->send(buffer, 4, &client);
            }
        } while(bytes_read == 516);
        fclose(output);
    }
    
    return 0;
}