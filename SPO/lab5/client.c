#include "libNetwork.h"

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

int main(int argc, char *argv[])
{
    if (argc < 2) {
        printf("Укажите файл для передачи\n");
        exit(1);
    }
    struct sockaddr_in addr;
    addr.sin_family = AF_INET;
    addr.sin_port = htons(3425);
    addr.sin_addr.s_addr = htonl(INADDR_LOOPBACK);
    Network * net = new Network(0, &addr, 1);
    TFTP * client = new TFTP(net);
    client->sendFile(argv[1]);
    return 0;
}
