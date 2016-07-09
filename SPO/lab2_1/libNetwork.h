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
        int sock;
        int lost;
        sockaddr_in* peer;
    public:
        Network(int lost, sockaddr_in* peer, int needBind);
        void dumpMsg(char * buffer, int messageLen, int lost);
        void send(char * buffer, int messageLen);
        int recv(char * buffer, int messageLen);
        ~Network();
};