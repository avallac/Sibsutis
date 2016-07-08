#include "libTFTP.h"


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
