all:	clean sniff
sniff:	sniff.c
	g++ -o sniff sniff.c -lpcap -lcrypto
clean:
	rm -f *.o *.a sniff
