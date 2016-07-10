#include "libNetwork.h"
#include <time.h>

class TFTP
{
private:
    int attemp;
    Network * net;
    FILE *output;
    int num;
    unsigned long lastMess;
public:
    TFTP(Network * net);
    int getCode(char * buffer);
    int getNum(char * buffer);
    int getAck(int wCode);
    void msgWRQ(char * filename);
    void msgData(int num, char * data, int len);
    void sendFile(char * filename);
    void waitFile(char * buffer);
    int downloadFile();
    void msgAck(int num);
};