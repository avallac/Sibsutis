all:	clean server client
server:	server.c
	g++ -o server server.c -lsctp -fpermissive -lpthread
client:	client.c
	g++ -o client client.c -lsctp -fpermissive
clean:
	rm -f *.o *.a server client
