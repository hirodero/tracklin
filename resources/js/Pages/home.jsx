'use client'
import '../../css/app.css'; 
import { useState, useEffect } from 'react';
import Header from '../components/ui/header';
import { motion } from 'framer-motion';
import { Link } from '@inertiajs/react'
import { Note, SignIn, LearnMore, PersonalComputer, Pencil, Book, Clock } from '../components/ui/attributes';
const role='guest';
export default function Home() {
    const [toDo,setToDo] = useState(false)
    const size = 2;
  return (
    <div className="fixed inset-0 flex flex-col">
      <Header/>
      <div className='min-h-full'>
        <div className='h-[10%] w-full '/>
        <div className='flex flex-row w-[100%] h-[15%]'/>
          <div className="flex justify-center items-center flex-col h-[75%] rounded-2xl text-8xl [-webkit-text-stroke:0.4px_black] [text-shadow:_4px_4px_0_#000] font-extralight text-white">
            <motion.div 
            initial={{opacity:0, x:-100}}
            animate={{opacity:1, x:0}}
            transition={{duration:1}}
            className='flex flex-col justify-center items-center'>
              <p>
              Hello! 
              </p>
              <p>
              Welcome to Tracklin!
              </p>
            </motion.div>
            <div className='flex flex-row w-full h-full'>
              <div className='flex flex-col items-center w-[25%] h-full justify-baseline'>
                <motion.div 
                initial={{scale:0, opacity:0}}
                animate={{scale:1, opacity:1}}
                transition={{duration:0.9, ease:'circOut',type:'spring'}}
                className='flex flex-col w-[65%] h-full justify-center items-center'>
                  <Note/>
                </motion.div>
              </div>
              <div className='flex justify-between w-[50%] pt-5'>
                {
                  role!=='user'?(
                  <>
                    <motion.button 
                    initial={{scale:0, opacity:0}}
                    animate={{scale:1, opacity:1}}
                    transition={{duration:0.9, ease:'circOut',type:'spring'}}
                    className='w-[300px] h-[100px]'>
                      <SignIn/>
                    </motion.button>
                    <motion.button 
                    initial={{scale:0, opacity:0}}
                    animate={{scale:1, opacity:1}}
                    transition={{duration:0.9, ease:'easeInOut', type:'spring', delay:0.2}}
                    className='w-[300px] h-[100px]'>
                        <Link href={'/about'}>
                            <LearnMore/>
                        </Link>
                    </motion.button>
                  </>  
                  ):(
                    <>
                      <div className="flex flex-col w-[50%] items-center justify-baseline h-full gap-6">
                        <motion.div
                        initial={{opacity:0}}
                        animate={{opacity:1}}
                        transition={{duration:0.4, ease:'easeInOut'}}
                        onHoverStart={()=>setToDo(true)} 
                        onHoverEnd={()=>setToDo(false)}
                        className="flex justify-center items-center hover:opacity-75 cursor-pointer hover:scale-102 transition duration-500 ease-in-out w-[175px] h-[175px] bg-white/90 outline-4 outline-blue-600 ring-8 rounded-full">
                            <Pencil props={toDo} size={3} />
                            <Book props={toDo} size={size}/>
                        </motion.div>
                        <p className="text-4xl">
                            To-Do List 
                        </p>
                      </div>
                      <div className="flex flex-col w-[50%] items-center justify-baseline h-full gap-6">
                        <motion.div
                        initial={{opacity:0}}
                        animate={{opacity:1}}
                        transition={{duration:0.4, ease:'easeInOut'}}
                        className="flex justify-center items-center hover:opacity-75 cursor-pointer hover:scale-102 transition duration-500 ease-in-out w-[175px] h-[175px] bg-white/90 outline-4 outline-blue-600 ring-8 rounded-full">
                            <Clock size={size}/>
                        </motion.div>
                        <p className="text-4xl">
                            Set Alarm
                        </p>
                    </div>
                    </>
                    
                  )
                }
              </div>  
              <div className='flex flex-row w-[25%] h-full justify-center'>
                <motion.div 
                initial={{scale:0, opacity:0}}
                animate={{scale:1, opacity:1}}
                transition={{duration:0.9, ease:'circOut',type:'spring'}}
                className='w-[180px] h-[170px]'>
                  <PersonalComputer/>
                </motion.div>
              </div>
          </div>
        </div>
      </div>
    </div>
  );
}
