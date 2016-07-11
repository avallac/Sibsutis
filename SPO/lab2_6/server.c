#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <time.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <netinet/in.h>
#include <netinet/sctp.h>
#include <pthread.h>


class FileManager
{
protected:
    int status, fLen;
    char path[128];
    FILE *output;
public:
    FileManager()
    {
        status = 1;
    }
    int downloadFile(char * buf, int bytes_read)
    {
        if (status == 1) {
            fLen = buf[0];
            memcpy(path, "upload/", 7);
            memcpy(path + 7, buf + 1, fLen);
            path[fLen + 7] = 0;
            printf("Запись в файл: '%s'\n", path);
            output = fopen(path, "wb");
            status++;
        } else if (status == 2) {
            if(bytes_read <= 0) return -1;
            fwrite(buf, bytes_read, 1, output);
        }
    }
    ~FileManager()
    {
        if (status == 2) {
            fclose(output);
        }
    }
};

int prepairToWork(int port)
{
    int listenSock;
    struct sockaddr_in servaddr;
    struct sctp_initmsg initmsg;
    struct sctp_event_subscribe events;
    listenSock = socket( AF_INET, SOCK_STREAM, IPPROTO_SCTP );
    bzero( (void *)&servaddr, sizeof(servaddr) );
    servaddr.sin_family = AF_INET;
    servaddr.sin_addr.s_addr = htonl( INADDR_ANY );
    servaddr.sin_port = htons(port);
    bind( listenSock, (struct sockaddr *)&servaddr, sizeof(servaddr) );
    memset( &events, 0, sizeof(events) );
    events.sctp_data_io_event = 1;
    setsockopt( listenSock, SOL_SCTP, SCTP_EVENTS, &events, sizeof(events) );
    socklen_t namelen = sizeof(servaddr);
    if (getsockname(listenSock, (struct sockaddr *)&servaddr, &namelen) < 0){
        exit(1);
    }
    printf("Выделен порт: %i\n", ntohs(servaddr.sin_port));
    listen( listenSock, 5 );
    return listenSock;
}
void handler(int *connSock)
{
    FileManager * fm;
    struct sctp_sndrcvinfo sndrcvinfo;
    int in, flags;
    char buffer[1024];
    fm = NULL;
    while(in = sctp_recvmsg( connSock, buffer, sizeof(buffer), (struct sockaddr *)NULL, 0, &sndrcvinfo, &flags )) {
        if (in == -1) break;
        if (sndrcvinfo.sinfo_stream == 1) {
            printf("[%d]Поток %d: [%i]\n", connSock, sndrcvinfo.sinfo_stream, in);
            if (!fm) {
                fm = new FileManager();
            }
            fm->downloadFile(buffer, in);
        } else {
            printf("[%d]Поток %d: %s [%i]\n", connSock, sndrcvinfo.sinfo_stream, buffer, in);
        }
    }
    printf("[%d]Закрытие[%i]\n", connSock, in);
    close(connSock);
    if (fm) {
        delete fm;
    }
    pthread_exit(NULL);
}

int main(int argc, char *argv[])
{
    char buffer[1024];
    int listenSock, connSock, in, port;
        if (argc > 1) {
        port = atoi(argv[1]);
    } else {
        port = 0;
    }
    listenSock = prepairToWork(port);
    while (1) {
        bzero(buffer, 1024);
        connSock = accept( listenSock, (struct sockaddr *)NULL, (int *)NULL );
        printf("Входящее подключение....\n");
        pthread_t id;
        pthread_create(&id, NULL, handler, connSock);
    }
    return 0;
}
