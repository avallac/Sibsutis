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

struct hostent * resolv(char * host)
{
    unsigned int i=0;
    struct hostent *hp = gethostbyname(host);
    if (hp == NULL) {
        printf("gethostbyname() failed\n");
        exit(1);
    } else {
        printf("Resolv - %s = ", hp->h_name);
        while ( hp->h_addr_list[i] != NULL) {
            printf( "%s ", inet_ntoa( *( struct in_addr*)( hp -> h_addr_list[i])));
            i++;
        }
        printf("\n");
    }
    return hp;
}

int prepairConnect(char * host, char * port)
{
    int sock;
    struct sockaddr_in addr;
    sock = socket(AF_INET, SOCK_STREAM, 0);
    if(sock < 0) {
        perror("socket");
        exit(1);
    }
    addr.sin_family = AF_INET;
    struct hostent *hp = resolv(host);
    addr.sin_port = htons(atoi(port));
    memcpy(&addr.sin_addr, hp->h_addr_list[0], hp->h_length);
    if(connect(sock, (struct sockaddr *)&addr, sizeof(addr)) < 0) {
        perror("connect");
        exit(2);
    }
    return sock;
}

int main(int argc, char *argv[])
{
    FILE *input;
    int length, size;
    char buf[512];
    if (argc < 4) {
        perror("Use format: client HOST POST NUM");
        exit(1);
    }
    int sock = prepairConnect(argv[1], argv[2]);
    buf[0] = argv[3][0];
    while(1) {
        printf("Отправка данных\n");
        send(sock, buf, 1, 0);\
        sleep(argv[3][0] - '0');
    }
    close(sock);
    return 0;
}