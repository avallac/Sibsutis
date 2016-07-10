#include "libTFTP.h"

TFTP::TFTP(Network * net)
{
    attemp = 0;
    this->net = net;
}
int TFTP::getAck(int wCode)
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
void TFTP::msgWRQ(char * filename)
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
void TFTP::msgData(int num, char * data, int len)
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
void TFTP::sendFile(char * filename)
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
        sleep(1);
    } while(size == 512);
    fclose(input);
}
int TFTP::getCode(char * buffer)
{
    return buffer[0] * 256 + buffer[1];
}

int TFTP::getNum(char * buffer)
{
    return buffer[2] * 256 + buffer[3];
}
void TFTP::waitFile(char * buffer)
{
    char path[128];
    if (getCode(buffer) == 2) {
        memcpy(path, "upload/", 7);
        memcpy(path + 7, buffer + 2, strlen(buffer + 2) + 1);
        printf("[%d]Запись в файл: %s\n", net->getSock(), path);
        output = fopen(path, "wb");
        msgAck(0);
        if (getCode(buffer) == 2) {
            num = 1;
            lastMess = time(NULL);
        }
    }
}
int TFTP::downloadFile()
{
    char buffer[1024];
    int bytes_read;
    bytes_read = net->recv(buffer, 1024);
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
    }
    if (bytes_read == -1) {
        if ((time(NULL) - lastMess) < 10) {
            return 1;
        } else {
            printf("Подключение слишком долго не активно. Обрыв\n");
        }
    }
    if(bytes_read == 516 || bytes_read == -1) {
        return 1;
    }
    fclose(output);
    printf("Передача окончена\n");
    return -1;
}
void TFTP::msgAck(int num)
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