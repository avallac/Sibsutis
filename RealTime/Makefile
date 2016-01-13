all:	clean lab1 lab2_1 lab2_2
lab1:	lab1.cpp
	g++ -o lab1 lab1.cpp -I/usr/X11R6/include -L/usr/X11R6/lib -lX11 -Wno-c++11-compat-deprecated-writable-strings
lab2_1:	lab2.cpp
	g++ -o lab2 lab2.cpp -I/usr/X11R6/include -L/usr/X11R6/lib -lX11 -Wno-c++11-compat-deprecated-writable-strings
lab2_2:	lab2_2.cpp
	g++ -o lab2_2 lab2_2.cpp -I/usr/X11R6/include -L/usr/X11R6/lib -lX11 -Wno-c++11-compat-deprecated-writable-strings
clean:
	rm -f *.o *.a lab1 lab2 lab2_2
