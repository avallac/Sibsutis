all:	clean server client
server:	server.c
	g++ -o server server.c -fpermissive  -lpthread
client:	client.c
	g++ -o client client.c
clean:
	rm -f *.o *.a server client
