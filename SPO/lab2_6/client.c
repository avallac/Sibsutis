#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <netinet/in.h>
#include <netinet/sctp.h>
#include <arpa/inet.h>
#include <netdb.h>

#define MY_PORT_NUM 62324 /* This can be changed to suit the need and should be same in server and client*/

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


int main(int argc, char *argv[])
{
    char buf[1024], out[1024];
    int connSock, in, i, ret, flags, size;
    struct sockaddr_in servaddr;
    struct sctp_status status;
    if (argc < 4) {
        perror("Use format: client <HOST> <PORT> <FILE>");
        exit(1);
    }
    connSock = socket( AF_INET, SOCK_STREAM, IPPROTO_SCTP );
    bzero( (void *)&servaddr, sizeof(servaddr) );
    servaddr.sin_family = AF_INET;
    servaddr.sin_port = htons(atoi(argv[2]));
    struct hostent *hp = resolv(argv[1]);
    memcpy(&servaddr.sin_addr, hp->h_addr_list[0], hp->h_length);
    connect( connSock, (struct sockaddr *)&servaddr, sizeof(servaddr) );
    buf[0] = strlen(argv[3]);
    memcpy(buf + 1, argv[3], strlen(argv[3]));
    sctp_sendmsg( connSock, (void *)buf, (size_t)(strlen(argv[3]) + 1), NULL, 0, 0, 0, 1, 0, 0 );
    sprintf(buf, "Отправлено: %i байт", strlen(argv[3]) + 1);
    printf("%s\n", buf);
    sctp_sendmsg( connSock, (void *)buf, (size_t)(strlen(buf) + 1), NULL, 0, 0, 0, 0, 0, 0 );
    FILE * input = fopen(argv[3], "rb");
    while(size = fread(buf, 1, 512, input)) {
        sprintf(out, "Отправлено: %i байт", size);
        printf("%s\n", out);
        sctp_sendmsg(connSock, (void *)buf, (size_t)(size), NULL, 0, 0, 0, 1, 0, 0);
        sctp_sendmsg( connSock, (void *)out, (size_t)(strlen(out) + 1), NULL, 0, 0, 0, 0, 0, 0 );
        sleep(1);
    }
    close(connSock);
    return 0;
}